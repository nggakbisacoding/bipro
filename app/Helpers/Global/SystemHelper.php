<?php

if (! function_exists('includeFilesInFolder')) {
    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param $folder
     */
    function includeFilesInFolder($folder)
    {
        try {
            $rdi = new RecursiveDirectoryIterator($folder);
            $it = new RecursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (! $it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

if (! function_exists('includeRouteFiles')) {
    /**
     * @param $folder
     */
    function includeRouteFiles($folder)
    {
        includeFilesInFolder($folder);
    }
}

if (! function_exists('toPascalCase')) {
    function toPascalCase($string) {
        return \Str::studly($string);
    }
}


if (! function_exists('resolvePage')) {
    function resolvePage(string $name, bool $onlyPaths = false) {
        if (!strpos($name, "::")) {
    
            return "resources/js/Pages/{$name}.tsx";
        }
    
        [$module, $page] = explode("::", $name);
    
        $pagePath = implode('/', array_map("toPascalCase", explode('.', $page)));
        $pages = 'Modules/' . toPascalCase($module) . "/Resources/pages/" . toPascalCase($pagePath) . ".tsx";
    
        if ($onlyPaths) {
            return toPascalCase($pagePath);
        }

        return $pages;
    }
    
}