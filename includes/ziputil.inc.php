<?php  

class ZipUtil {

    /**
     * Zips a file
     * 
     * @param string $zipname The filepath of the zip
     * @param array $files The files that will be zipped
     * @return bool 
     */
    public static function zip(string $zipname, array $files) : bool {

        $zip = new ZipArchive;

        try {
            $zip->open($zipname, file_exists($zipname) ? ZipArchive::OVERWRITE : ZipArchive::CREATE);

            foreach ($files as $file) 
                $zip->addFromString(basename($file), file_get_contents($file));

            return true;
        } catch(Exception $e) {
            return false;
        } finally {
            $zip->close();
        }
    }

    /**
     * Extracts files from a zip file
     * and stores it in the destination path
     * 
     * @param string $zipname The filepath of the zip file
     * @param string $destination The destination where the contents will be extracted to
     * @return bool
     */
    public static function unzip(string $zipname, string $destination) : bool {

        $zip = new ZipArchive;

        try {
            $zip->open($zipname);
            $zip->extractTo($destination);

            return true;
        } catch(Exception $e) {
            return false;
        } finally {
            $zip->close();
        }
    }

}

?>