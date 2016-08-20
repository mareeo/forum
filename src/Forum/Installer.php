<?php

namespace Forum\Forum;


class Installer {
    public static function postInstall() {
        require 'config.php';

        if(!is_dir(FS_IMG_DIR)) {
            mkdir(FS_IMG_DIR);
        }

        if(!is_dir(FS_THUMB_DIR)) {
            mkdir(FS_THUMB_DIR);
        }

    }
}
