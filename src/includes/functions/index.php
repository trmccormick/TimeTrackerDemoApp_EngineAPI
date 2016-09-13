<?php
    function determineAction($class, $action, $item){
        $localvars = localvars::getInstance();
        $validate  = new validate;
        if(class_exists($class)){
            $myClass   = new $class;
            $pageData  = "";
            // record Id Set to null
            $id = null;
            // create an array of valid actions
            $validActions = array('create', 'add', 'read', 'view', 'update', 'edit', 'delete', 'confirmDelete', 'play');
            // this is an $id only
            // not null and not an empty string
            if(!isnull($item) && !is_empty($item) && $validate->integer($item)){
                $id = $item;
            }
            // get a specific record or determine what to do
            if(!isnull($action) || in_array($action, $validActions)){
                if($validate->integer($action)){
                    $pageData = $myClass->getRecords($action);
                }
                else {
                    switch ($action) {
                        case 'create':
                            break;
                        case 'add':
                            break;
                        case 'update':
                            break;
                        case 'edit':
                            if(isnull($id)){
                                $pageData = $myClass->setupForm();
                            }
                            else{
                                $pageData = $myClass->setupForm($id);
                            }
                            break;
                        break;
                        case 'delete':
                            if(!isnull($id)){
                                $pageData = $myClass->deleteRecord($id);
                            } else {
                                $pageData = $myClass->deleteRecord();
                            }
                            break;
                        case 'confirmDelete':
                            if(!isnull($id)){
                                $pageData = "Are you sure you want to delete this record?";
                                $pageData .= $myClass->renderDeleteData($id);
                            } else {
                                header('Location:/404Error?invalidId=true');
                            }
                            break;
                        case 'read':
                            break;
                        case 'view':
                            // if isnull $id get all records
                            if(isnull($id)){
                                $pageData = $myClass->renderDataTable();
                            }
                            else{
                                $pageData = $myClass->renderSingleRecord($id);
                            }
                            break;
                        case 'play':
                            // if isnull $id get all records
                            if(isnull($id)){
                                $pageData = $myClass->renderDataTable();
                            }
                            else{
                              $filetype = file_mime_type('.'.getFileName($id));
                              switch ($filetype) {
                                 case "audio/mpeg; charset=binary":
                                   $pageData = $myClass->playAudioFile($id);
                                   break;
                                 case "video/x-msvideo; charset=binary":
                                   $pageData = $myClass->playVideoFile($id);
                                   break;
                                 default:
                                   var_dump($filetype);
                                   die();
                              }
                            }
                            break;
                        default:
                    }
                }
            } else {
                 $pageData = $myClass->renderDataTable();
            }
            return $pageData;
        }
        else {
            header('Location:/404Error?ClassError=true');
        }
    }

    function file_mime_type($file, $encoding=true) {
      $mime=false;

      if (function_exists('finfo_file')) {
        $finfo = finfo_open(FILEINFO_MIME);
        $mime = finfo_file($finfo, $file);
        finfo_close($finfo);
      }
      else if (substr(PHP_OS, 0, 3) == 'WIN') {
        $mime = mime_content_type($file);
      }
      else {
        $file = escapeshellarg($file);
        $cmd = "file -iL $file";

        exec($cmd, $output, $r);

        if ($r == 0) {
            $mime = substr($output[0], strpos($output[0], ': ')+2);
        }
      }

      if (!$mime) {
        return false;
      }

      if ($encoding) {
        return $mime;
      }

      return substr($mime, 0, strpos($mime, '; '));
    }

    function getFileName($id){
        $localvars   = localvars::getInstance();
        $validate    = new validate;
        $mediaArchive   = new mediaArchive;
        $returnValue = "";
        if(isnull($id) && !$validate->integer($id)){
            throw new Exception('not valid integer');
            return false;
        }
        else {
            $data        = $mediaArchive->getRecords($id);
            $returnValue = $data[0]['filepath'].$data[0]['filename'];
            return $returnValue;
        }
    }

    function getCompanyName($id){
        $localvars   = localvars::getInstance();
        $validate    = new validate;
        $customers   = new Customers;
        $returnValue = "";
        if(isnull($id) && !$validate->integer($id)){
            throw new Exception('not valid integer');
            return false;
        }
        else {
            $data        = $customers->getRecords($id);
            $returnValue = $data[0]['companyName'];
            return $returnValue;
        }
    }
?>
