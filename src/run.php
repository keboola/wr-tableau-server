<?php
use Symfony\Component\Yaml\Yaml;
require_once(dirname(__FILE__) . "/../vendor/autoload.php");
$arguments = getopt("d::", array("data::"));
if (!isset($arguments["data"])) {
    print "Data folder not set.";
    exit(1);
}
$config = Yaml::parse(file_get_contents($arguments["data"] . "/config.yml"));

try {
    $writer = new \Keboola\TableauServerWriter\Writer(
        $config["parameters"]["server_url"],
        $config["parameters"]["username"],
        $config["parameters"]["password"],
        $config["parameters"]["site"]
    );

    $filesCount = 0;
    foreach (glob($arguments["data"] . "/in/files/*.tde") as $filename) {
        $writer->publishFile($filename);
        $filesCount++;
    }
    $writer->logout();

} catch (\Keboola\TableauServerWriter\Exception $e) {
    print $e->getMessage();
    exit(1);
}
print "Processed {$filesCount} files.";
exit(0);