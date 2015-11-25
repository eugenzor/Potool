<?php
namespace Potool\Model;
/**
 * Created by JetBrains PhpStorm.
 * User: eugene
 * Date: 1/30/13
 * Time: 4:33 PM
 * To change this template use File | Settings | File Templates.
 */
class Utils
{

    static function getFileList($path, $options=null)
    {
        if(!is_dir($path)){
            throw new FileListException("Path must be dir");
        }
        $files = array();
        $list = scandir($path);

        foreach($list as $item){
            if ($item == '.' || $item == '..'){
                continue;
            }

            if (in_array($item, array('.svn', '.hg', '.git'))){
                continue;
            }

            $fullName = $path . DIRECTORY_SEPARATOR . $item;

            //Handle type option
            if (isset($options['type'])){
                if ($options['type'] == 'f' && !is_file($fullName)){
                    continue;
                }

                if ($options['type'] == 'd' && !is_dir($fullName)){
                    continue;
                }
            }


            //Handle ext option
            if (isset($options['ext'])){
                if (!preg_match('/\.'.preg_quote($options['ext']).'$/i', $item)){
                    continue;
                }
            }

            //Handle pattern option
            if (isset($options['pattern'])){
                if (!preg_match($options['pattern'], $item)){
                    continue;
                }
            }
            $files[]=$item;
        }

        return $files;
    }
}
