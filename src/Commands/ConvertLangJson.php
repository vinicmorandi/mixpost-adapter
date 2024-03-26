<?php

namespace SaguiAi\MixpostAdapter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Util;
use Illuminate\Support\Facades\File;

class ConvertLangJson extends Command
{
    protected $signature = 'mixpost:convert-lang-json';

    protected $description = 'Convert language files to json files';

    protected array $jsonArray = [];

    public function handle(): void
    {
        $this->createJsonArray();
        $this->createJsonFiles();

        $this->info('All language files have been converted to json files!');
    }

    protected function getLanguageFiles(string $locale): array
    {
        $files = File::files("{$this->langFolderPath()}/$locale");

        $array = [];

        foreach ($files as $file) {
            $jsons = collect(Util::config('locales'))->map(function ($lang) {
                return $lang['long'] . '.json';
            })->toArray();

            if (!in_array($file->getFilename(), $jsons)) {
                $array[] = Str::before($file->getFilename(), '.php');
            }
        }

        return $array;
    }

    protected function createJsonArray(): void
    {
        foreach (Util::config('locales') as $locale) {
            foreach ($this->getLanguageFiles($locale['long']) as $group) {
                $keys = $this->readLanguageFileAndGetData($locale['long'], $group);

                $onlyFilledKeys = collect($keys)->filter(function ($value) {
                    return $value;
                })->toArray();

                if (count($onlyFilledKeys)) {
                    if (!Str::endsWith($group, '_back')) {
                        $this->jsonArray[$locale['long']][$group] = $onlyFilledKeys;
                    }
                }
            }
        }
    }

    public function readLanguageFileAndGetData(string $language, string $fileName)
    {
        $file = "{$this->langFolderPath()}/$language/$fileName.php";

        if (File::exists($file)) {
            return require $file;
        }

        return (object)[];
    }

    protected function createJsonFiles(): void
    {
        foreach ($this->jsonArray as $language => $contain) {
            $path = "{$this->langFolderJsonPath()}/$language.json";

            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0775, true);
            }

            $output = json_encode($this->adjustArray($contain), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            file_put_contents($path, $output);
        }
    }

    protected function adjustArray(array $arr): array
    {
        $res = [];

        foreach ($arr as $key => $val) {
            $key = $this->removeEscapeCharacter($this->adjustString($key));

            if (is_array($val)) {
                $res[$key] = $this->adjustArray($val);
            } else {
                $res[$key] = $this->removeEscapeCharacter($this->adjustString($val));
            }
        }

        return $res;
    }

    protected function removeEscapeCharacter($s): string
    {
        $escaped_escape_char = preg_quote('!', '/');

        return preg_replace_callback(
            "/{$escaped_escape_char}(:\w+)/",
            function ($matches) {
                return mb_substr($matches[0], 1);
            },
            $s
        );
    }

    protected function adjustString($s): string
    {
        if (!is_string($s)) {
            return $s;
        }

        $escaped_escape_char = preg_quote('!', '/');

        return preg_replace_callback(
            "/(?<!mailto|tel|{$escaped_escape_char}):\w+/",
            function ($matches) {
                return '{' . mb_substr($matches[0], 1) . '}';
            },
            $s
        );
    }

    protected function langFolderPath(): string
    {
        return __DIR__ . '/../../resources/lang';
    }

    protected function langFolderJsonPath(): string
    {
        return __DIR__ . '/../../resources/lang-json';
    }
}
