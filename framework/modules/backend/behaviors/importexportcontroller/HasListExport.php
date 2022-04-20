<?php namespace Backend\Behaviors\ImportExportController;

use Lang;
use Response;
use ApplicationException;

/**
 * HasListExport contains logic for imports
 */
trait HasListExport
{
    /**
     * checkUseListExportMode
     */
    protected function checkUseListExportMode()
    {
        if (!$useList = $this->getConfig('export[useList]')) {
            return false;
        }

        if (!$this->controller->isClassExtendedWith(\Backend\Behaviors\ListController::class)) {
            throw new ApplicationException(Lang::get('backend::lang.import_export.behavior_missing_uselist_error'));
        }

        if (is_array($useList)) {
            $listDefinition = array_get($useList, 'definition');
        }
        else {
            $listDefinition = $useList;
        }

        return $this->exportFromList($listDefinition);
    }

    /**
     * exportFromList outputs the list results as a CSV export.
     * @param string $definition
     * @param array $options
     */
    public function exportFromList($definition = null, $options = [])
    {
        $lists = $this->controller->makeLists();
        $widget = $lists[$definition] ?? reset($lists);

        // Parse options
        $defaultOptions = [
            'file_format' => $this->getConfig('defaultFormatOptions[file_format]', 'json'),
            'delimiter' => $this->getConfig('defaultFormatOptions[delimiter]', ','),
            'enclosure' => $this->getConfig('defaultFormatOptions[enclosure]', '"'),
            'escape' => $this->getConfig('defaultFormatOptions[escape]', '\\'),
        ];
        $options = array_merge($defaultOptions, $options);

        // Prepare output
        $fileFormat = $options['file_format'] ?? 'json';
        $filename = e($this->makeExportFileName($fileFormat));
        $output = '';

        switch ($fileFormat) {
            case 'json':
                $output = $this->processExportDataAsJson($widget, $options);
                break;
            case 'csv':
            case 'csv_custom':
                $output = $this->exportFromListAsCsv($widget, $options);
                break;
        }

        // Response
        $response = Response::make();
        $response->header('Content-Type', 'text/csv');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Disposition', sprintf('%s; filename="%s"', 'attachment', $filename));
        $response->setContent($output);
        return $response;
    }
}
