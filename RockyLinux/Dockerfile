ARG CI_REGISTRY=docker.io/library
FROM ${CI_REGISTRY}/rockylinux:latest

LABEL org.opencontainers.image.title="RockyLinux Updated" \
    org.opencontainers.image.authors="Andrey Leshkevich <magicgts@gmail.com>" \
    org.opencontainers.image.description="RockyLinux Updated" \
    org.opencontainers.image.version="0.8"


RUN set -eux && \
    dnf update -y && \
    dnf -y install epel-release && \
    REPOLIST="baseos,appstream,epel,extras" && \
    INSTALL_PKGS="curl \
    bash-completion \
    nano \
    less \
    python3 \
    supervisor \
    findutils \
    glibc-locale-source \
    curl \
    unzip \
    boost-program-options \
    yum-utils" && \
    dnf -y install \
    --disablerepo "*" \
    --enablerepo "${REPOLIST}" \
    --setopt=tsflags=nodocs \
    --setopt=install_weak_deps=False \
    --best \
    ${INSTALL_PKGS} && \
    dnf -y clean all && \
    rm -rf /var/cache/yum /var/lib/yum/yumdb/* /usr/lib/udev/hwdb.d/* && \
    rm -rf /var/cache/dnf /etc/udev/hwdb.bin /root/.pki
