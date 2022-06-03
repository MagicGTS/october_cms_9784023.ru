<?php namespace System\Classes;

use App;
use Markdown;
use System\Models\MailPartial;
use System\Models\MailTemplate;
use System\Models\MailBrandSetting;
use System\Helpers\View as ViewHelper;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * MailManager manages Mail sending functions
 *
 * @method static MailManager instance()
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class MailManager
{
    use \October\Rain\Support\Traits\Singleton;

    /**
     * @var array Cache of registration callbacks.
     */
    protected $callbacks = [];

    /**
     * @var array A cache of customised mail templates.
     */
    protected $templateCache = [];

    /**
     * @var array List of registered templates in the system
     */
    protected $registeredTemplates;

    /**
     * @var array List of registered partials in the system
     */
    protected $registeredPartials;

    /**
     * @var array List of registered layouts in the system
     */
    protected $registeredLayouts;

    /**
     * @var bool Internal marker for rendering mode
     */
    protected $isHtmlRenderMode = false;

    /**
     * addContentFromEvent handles adding content from the `mailer.beforeAddContent` event
     */
    public function addContentFromEvent($message, $view = null, $plain = null, $raw = null, $data = [])
    {
        // Dual views have been supplied, this is Laravel implementation
        if ($view !== null && $plain !== null) {
            return false;
        }

        // When "plain-text only" email is sent, $view is null, this sets the flag appropriately
        $plainOnly = $view === null;

        if ($raw === null) {
            return $this->addContentToMailer($message, $view ?: $plain, $data, $plainOnly);
        }
        else {
            return $this->addRawContentToMailer($message, $raw, $data, $plainOnly);
        }
    }

    /**
     * addRawContentToMailer is the same as `addContentToMailer` except with raw content
     *
     * @return bool
     */
    public function addRawContentToMailer($message, $content, $data)
    {
        $template = new MailTemplate;

        $template->fillFromContent($content);

        $this->addContentToMailerInternal($message, $template, $data);

        return true;
    }

    /**
     * addContentToMailer function hijacks the `addContent` method of the
     * `October\Rain\Mail\Mailer` class, using the `mailer.beforeAddContent` event.
     *
     * @param \Illuminate\Mail\Message $message
     * @param string $code
     * @param array $data
     * @param bool $plainOnly Add only plain text content to the message
     * @return bool
     */
    public function addContentToMailer($message, $code, $data, $plainOnly = false)
    {
        if (!is_string($code)) {
            return false;
        }

        if (isset($this->templateCache[$code])) {
            $template = $this->templateCache[$code];
        }
        else {
            $this->templateCache[$code] = $template = MailTemplate::findOrMakeTemplate($code);
        }

        if (!$template) {
            return false;
        }

        $this->addContentToMailerInternal($message, $template, $data, $plainOnly);

        return true;
    }

    /**
     * addContentToMailerInternal is an internal method used to share logic
     * between `addRawContentToMailer` and `addContentToMailer`
     *
     * @param \Illuminate\Mail\Message $message
     * @param MailTemplate $template
     * @param array $data
     * @param bool $plainOnly Add only plain text content to the message
     * @return void
     */
    protected function addContentToMailerInternal($message, $template, $data, $plainOnly = false)
    {
        /*
         * Inject global view variables
         */
        $globalVars = ViewHelper::getGlobalVars();
        if (!empty($globalVars)) {
            $data = (array) $data + $globalVars;
        }

        /*
         * Subject
         */
        $sMessage = $message->getSymfonyMessage();

        if (empty($sMessage->getSubject())) {
            $message->subject($this->parseTwig($template->subject, $data));
        }

        $data += [
            'subject' => $sMessage->getSubject()
        ];

        // HTML contents
        if (!$plainOnly) {
            $html = $this->renderTemplate($template, $data);

            $message->html($html);
        }

        // Text contents
        $text = $this->renderTextTemplate($template, $data);

        $message->text($text);
    }

    //
    // Rendering
    //

    /**
     * render the Markdown template into HTML
     * @param  string  $content
     * @param  array  $data
     * @return string
     */
    public function render($content, $data = [])
    {
        if (!$content) {
            return '';
        }

        $html = $this->parseTwig($content, $data);

        $html = Markdown::parseNoIndent($html);

        return $html;
    }

    /**
     * renderTemplate
     */
    public function renderTemplate($template, $data = [])
    {
        $this->isHtmlRenderMode = true;

        $html = $this->render($template->content_html, $data);

        $css = MailBrandSetting::renderCss();

        $disableAutoInlineCss = false;

        if ($template->layout) {
            $disableAutoInlineCss = array_get($template->layout->options, 'disable_auto_inline_css', $disableAutoInlineCss);

            $html = $this->parseTwig($template->layout->content_html, [
                'content' => $html,
                'css' => $template->layout->content_css,
                'brandCss' => $css
            ] + (array) $data);

            $css .= PHP_EOL . $template->layout->content_css;
        }

        if (!$disableAutoInlineCss) {
            $html = (new CssToInlineStyles)->convert($html, $css);
        }

        return $html;
    }

    /**
     * Render the Markdown template into text.
     * @param $content
     * @param array $data
     * @return string
     */
    public function renderText($content, $data = [])
    {
        if (!$content) {
            return '';
        }

        $text = $this->parseTwig($content, $data);

        $text = html_entity_decode(preg_replace("/[\r\n]{2,}/", "\n\n", $text), ENT_QUOTES, 'UTF-8');

        return $text;
    }

    /**
     * renderTextTemplate
     */
    public function renderTextTemplate($template, $data = [])
    {
        $this->isHtmlRenderMode = false;

        $templateText = $template->content_text;

        if (!empty($template->content_text)) {
            $templateText = $template->content_html;
        }

        $text = $this->renderText($templateText, $data);

        if ($template->layout) {
            $text = $this->parseTwig($template->layout->content_text, [
                'content' => $text
            ] + (array) $data);
        }

        return $text;
    }

    /**
     * renderPartial
     */
    public function renderPartial($code, array $params = [])
    {
        if (!$partial = MailPartial::findOrMakePartial($code)) {
            return '<!-- Missing partial: '.$code.' -->';
        }

        if ($this->isHtmlRenderMode) {
            $content = $partial->content_html;
        }
        else {
            $content = $partial->content_text ?: $partial->content_html;
        }

        if (!strlen(trim($content))) {
            return '';
        }

        return $this->parseTwig($content, $params);
    }

    /**
     * parseTwig parses Twig using the mailer environment
     */
    protected function parseTwig($contents, $vars = [])
    {
        $twig = App::make('twig.environment.mailer');
        $template = $twig->createTemplate($contents);
        return $template->render($vars);
    }

    //
    // Registration
    //

    /**
     * Loads registered mail templates from modules and plugins
     * @return void
     */
    public function loadRegisteredTemplates()
    {
        // Load module templates
        foreach ($this->callbacks as $callback) {
            $callback($this);
        }

        // Load plugin widgets
        foreach (PluginManager::instance()->getPlugins() as $pluginObj) {
            if (is_array($layouts = $pluginObj->registerMailLayouts())) {
                $this->registerMailLayouts($layouts);
            }

            if (is_array($templates = $pluginObj->registerMailTemplates())) {
                $this->registerMailTemplates($templates);
            }

            if (is_array($partials = $pluginObj->registerMailPartials())) {
                $this->registerMailPartials($partials);
            }
        }

        // Load app widgets
        if ($app = App::getProvider(\App\Provider::class)) {
            if (is_array($layouts = $app->registerMailLayouts())) {
                $this->registerMailLayouts($layouts);
            }

            if (is_array($templates = $app->registerMailTemplates())) {
                $this->registerMailTemplates($templates);
            }

            if (is_array($partials = $app->registerMailPartials())) {
                $this->registerMailPartials($partials);
            }
        }
    }

    /**
     * Returns a list of the registered templates.
     * @return array
     */
    public function listRegisteredTemplates()
    {
        if ($this->registeredTemplates === null) {
            $this->loadRegisteredTemplates();
        }

        return $this->registeredTemplates;
    }

    /**
     * Returns a list of the registered partials.
     * @return array
     */
    public function listRegisteredPartials()
    {
        if ($this->registeredPartials === null) {
            $this->loadRegisteredTemplates();
        }

        return $this->registeredPartials;
    }

    /**
     * Returns a list of the registered layouts.
     * @return array
     */
    public function listRegisteredLayouts()
    {
        if ($this->registeredLayouts === null) {
            $this->loadRegisteredTemplates();
        }

        return $this->registeredLayouts;
    }

    /**
     * Registers a callback function that defines mail templates.
     * The callback function should register templates by calling the manager's
     * registerMailTemplates() function. Thi instance is passed to the
     * callback function as an argument. Usage:
     *
     *     MailManager::registerCallback(function ($manager) {
     *         $manager->registerMailTemplates([...]);
     *     });
     *
     * @param callable $callback A callable function.
     */
    public function registerCallback(callable $callback)
    {
        $this->callbacks[] = $callback;
    }

    /**
     * Registers mail views and manageable templates.
     */
    public function registerMailTemplates(array $definitions)
    {
        if (!$this->registeredTemplates) {
            $this->registeredTemplates = [];
        }

        // Prior sytax where (key) code => (value) description
        if (!isset($definitions[0])) {
            $definitions = array_keys($definitions);
        }

        $definitions = array_combine($definitions, $definitions);

        $this->registeredTemplates = $definitions + $this->registeredTemplates;
    }

    /**
     * Registers mail views and manageable layouts.
     */
    public function registerMailPartials(array $definitions)
    {
        if (!$this->registeredPartials) {
            $this->registeredPartials = [];
        }

        $this->registeredPartials = $definitions + $this->registeredPartials;
    }

    /**
     * Registers mail views and manageable layouts.
     */
    public function registerMailLayouts(array $definitions)
    {
        if (!$this->registeredLayouts) {
            $this->registeredLayouts = [];
        }

        $this->registeredLayouts = $definitions + $this->registeredLayouts;
    }
}
