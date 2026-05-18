#!/bin/bash
# Fonctions communes — paquet YunoHost Moncine

MONCINE_PACKAGE_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

moncine_run_migrate() {
    local migrate_php="${install_dir}/lib/cli/migrate.php"
    if [[ ! -f "${migrate_php}" ]]; then
        ynh_exit 1 --message="migrate.php introuvable dans ${install_dir}/lib/cli/"
    fi
    export MONCINE_DATA_PATH="${data_dir}"
    if [[ -n "${domain:-}" ]]; then
        export MONCINE_BASE_URL="https://${domain}${path}"
    fi
    ynh_print_info "Application des migrations SQL Moncine…"
    php "${migrate_php}" || ynh_exit 1 --message="Échec des migrations Moncine"
}

# Copie www/, lib/, sql/, doc/ depuis le paquet vers $install_dir
moncine_copy_sources() {
    local dest="${1:?}"

    if [[ -d "${MONCINE_PACKAGE_ROOT}/www" ]]; then
        mkdir -p "${dest}/www/posters"
        rsync -a --delete --exclude 'posters/' \
            "${MONCINE_PACKAGE_ROOT}/www/" "${dest}/www/"
    fi

    local item
    for item in lib sql doc; do
        if [[ -d "${MONCINE_PACKAGE_ROOT}/${item}" ]]; then
            rsync -a --delete "${MONCINE_PACKAGE_ROOT}/${item}/" "${dest}/${item}/"
        fi
    done
}

moncine_prepare_data_dir() {
    mkdir -p "${data_dir}"
    chown -R "${app}:www-data" "${data_dir}"
    chmod 750 "${data_dir}"
}
