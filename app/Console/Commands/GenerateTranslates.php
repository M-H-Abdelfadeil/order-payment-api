<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateTranslates extends Command
{
    protected $signature = 'make:translates';
    protected $description = 'Generate English translation files based on Arabic ones';

    public function handle()
    {
        $this->info("Generating translation files...");

        $dirLang = base_path('lang/ar');
        $files = ["messages.php", "validation.php" , "order.php","payment.php"];

        foreach ($files as $item) {

            $file = $dirLang . "/" . $item;
            $fileBase = 'lang/ar/' . $item;
            $fileSave = base_path('lang/en/' . $item);

            if (!file_exists($file)) {
                $this->error("File not found: $file");
                continue;
            }

            $translates = include(base_path($fileBase));
            $data = [];

            foreach ($translates as $key => $value) {

                if (is_string($value)) {
                    $data[$key] = $key;
                } else {
                    $valuesArray = [];

                    foreach ($value as $keyValue => $valValue) {
                        if ($keyValue == "pending_end_time") {
                            $valuesArray[$keyValue] = "Publication period has expired";
                        } else {
                            $valuesArray[$keyValue] = ucfirst(str_replace("_", " ", $keyValue));
                        }
                    }

                    $data[$key] = $valuesArray;
                }
            }

            $fileContent = "<?php\n\nreturn " . var_export($data, true) . ";";

            file_put_contents($fileSave, $fileContent);

            $this->info("Generated: $fileSave");
        }

        $this->info("Done!");
        return 0;
    }
}
