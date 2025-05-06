#!/bin/bash

# Obtener los archivos PHP que están en staging, añadidos o modificados, y que estén dentro de la carpeta src/
changed_files=$(git diff --cached --name-only --diff-filter=ACM | grep '^src/.*\.php$')

if [ -n "$changed_files" ]; then
    echo "Analizando los siguientes archivos en src/:"
    echo "$changed_files"
    
    # Ejecuta PHP-CS-Fixer en modo dry-run con diff solo sobre los archivos filtrados
    ./vendor/bin/php-cs-fixer fix --dry-run --diff --config=.php-cs-fixer.dist.php $changed_files
else
    echo "No hay archivos PHP modificados en src/."
fi
