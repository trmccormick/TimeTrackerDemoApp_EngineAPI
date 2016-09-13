<?php
    include "includes/classes/FFmpeg.php";
    require_once('includes/getid3/getid3.php');

    class mediaArchive {
        public function getRecords($id = null){
            try {

                // call engine
                $engine    = EngineAPI::singleton();
                $localvars = localvars::getInstance();
                $db        = db::get($localvars->get('dbConnectionName'));
                $sql       = "SELECT * FROM `mediaArchive`";
                $validate  = new validate;

                // test to see if Id is present and valid
                if(!isnull($id) && $validate->integer($id)){
                    $sql .= sprintf('WHERE archiveID = %s LIMIT 1', $id);
                }

                // if no valid id throw an exception
                if(!$validate->integer($id) && !isnull($id)){
                    throw new Exception("I don't want to be tried!");
                }

                // get the results of the query
                $sqlResult = $db->query($sql);

                // if return no results
                // else return the data
                if ($sqlResult->error()) {
                    throw new Exception("ERROR SQL" . $sqlResult->errorMsg());
                }

                if ($sqlResult->rowCount() < 1) {
                   return array();
                }
                else {
                    $data = array();

                    while($row = $sqlResult->fetch()){
                        $data[] = $row;
                    }

                    return $data;
                }
            } catch (Exception $e) {
                errorHandle::errorMsg($e->getMessage());
            }
        }

        public function setupForm($id = null){
             try {
                // call engine
                $engine    = EngineAPI::singleton();
                $localvars = localvars::getInstance();
                $validate  = new validate;
                // create customer form
                $form = formBuilder::createForm('MediaArchive');
                $form->linkToDatabase( array(
                    'table' => 'mediaArchive'
                ));
                if(!is_empty($_POST) || session::has('POST')) {
                    $processor = formBuilder::createProcessor();
                    $processor->processPost();
                }
                // form titles
                $form->insertTitle = "";
                $form->editTitle   = "";
                $form->updateTitle = "";
                // if no valid id throw an exception
                if(!$validate->integer($id) && !isnull($id)){
                    throw new Exception(__METHOD__.'() - Not a valid integer, please check the integer and try again.');
                }
                // form information
                $form->addField(array(
                    'name'       => 'archiveID',
                    'type'       => 'hidden',
                    'value'      => $id,
                    'primary'    => TRUE,
                    'fieldClass' => 'id',
                    'showIn'     => array(formBuilder::TYPE_INSERT, formBuilder::TYPE_UPDATE),
                ));
                $form->addField(array(
                    'name'     => 'filename',
                    'label'    => 'Filename:',
                    'required' => FALSE,
                ));
                $form->addField(array(
                    'name'     => 'title',
                    'label'    => 'Title:',
                    'required' => FALSE,
                ));
                $form->addField(array(
                    'name'     => 'album',
                    'label'    => 'Album:',
                    'required' => FALSE
                ));
                $form->addField(array(
                    'name'       => 'author',
                    'label'      => 'Author:',
                    'required'   => FALSE
                ));
                $form->addField(array(
                    'name'       => 'track',
                    'label'      => 'Track:',
                    'required'   => FALSE
                ));
                $form->addField(array(
                    'name'     => 'year',
                    'label'    => 'Year:',
                    'required' => FALSE
                ));
                $form->addField(array(
                    'name'            => "length",
                    'label'           => 'Length:',
                    'required' => FALSE
                ));
                $form->addField(array(
                    'name'            => "lyric",
                    'label'           => 'Lyric:',
                    'type'            => 'textarea',
                    'required' => FALSE
                ));

                // buttons and submissions
                $form->addField(array(
                    'showIn'     => array(formBuilder::TYPE_UPDATE),
                    'name'       => 'update',
                    'type'       => 'submit',
                    'fieldClass' => 'submit',
                    'value'      => 'Update'
                ));
                $form->addField(array(
                    'showIn'     => array(formBuilder::TYPE_UPDATE),
                    'name'       => 'delete',
                    'type'       => 'delete',
                    'fieldClass' => 'delete hidden',
                    'value'      => 'Delete'
                ));
                $form->addField(array(
                    'showIn'     => array(formBuilder::TYPE_INSERT),
                    'name'       => 'insert',
                    'type'       => 'submit',
                    'fieldClass' => 'submit',
                    'value'      => 'Submit'
                ));
                return '{form name="MediaArchive" display="form"}';
            } catch (Exception $e) {
                errorHandle::errorMsg($e->getMessage());
            }
        }

        public function deleteFile($path, $filename){
          // Verify file still exists then delete
          if (file_exists('.' . $path . $filename)) {
           unlink('.' . $path . $filename);
           return "File deleted";
          }
          else {
            return "File not found";
          }
        }

        public function deleteRecord($id = null){
             try {
                // call engine
                $engine    = EngineAPI::singleton();
                $localvars = localvars::getInstance();
                $db        = db::get($localvars->get('dbConnectionName'));
                $validate  = new validate;

                // test to see if Id is present and valid
                if(isnull($id) || !$validate->integer($id)){
                    throw new Exception(__METHOD__.'() -Delete failed, improper id or no id was sent');
                }
                // Get Record to be deleted from database
                $dataRecord = self::getRecords($id);
                self::deleteFile($dataRecord[0]['filepath'], $dataRecord[0]['filename']);

                // SQL Results
                $sql = sprintf("DELETE FROM `mediaArchive` WHERE archiveID=%s LIMIT 1", $id);
                $sqlResult = $db->query($sql);

                if(!$sqlResult) {
                    throw new Exception(__METHOD__.'Failed to delete mediaArchive record.');
                }
                else {
                    return "Successfully deleted the mediaArchive record.";
                }

            } catch (Exception $e) {
                errorHandle::errorMsg($e->getMessage());
                return $e->getMessage();
            }
        }

        public function renderDeleteData($id){
          try {
            $engine    = EngineAPI::singleton();
            $localvars = localvars::getInstance();
            $validate  = new validate;

            if(isnull($id) || !$validate->integer($id)){
              throw new Exception('Id is null or not an integer.  Please try again.');
            }
            else {
              $dataRecord = self::getRecords($id);
              $output = "";

              foreach($dataRecord as $data){
                   $output .= sprintf("<div class='archiveRecord'>
                                          <h2 class='filename'>%s</h2>
                                           <div class='title'>
                                              <strong>Title:</strong>
                                              %s
                                           </div>
                                           <div class='tagInfo'>
                                              <div class='album'>%s</div>
                                              <div class='author'>%s</div>
                                              <div class='track'>%s</div>
                                              <div class='year'>%s</div>
                                              <div class='length'>%s</div>
                                              <div class='lyric'>%s</div>
                                          </div>
                                          <div class='actions'>
                                              <a href='/mediaArchive/delete/%s'> <span class='glyphicon glyphicon-ok'></span> </a>
                                              <a href='/mediaArchive'> <span class='glyphicon glyphicon-remove'></span> </a>
                                          </div>
                                      </div>",
                          $data['filename'],
                          $data['title'],
                          $data['album'],
                          $data['author'],
                          $data['track'],
                          $data['year'],
                          $data['length'],
                          $data['lyric'],
                          $data['archiveID']
                  );
                }

                return $output;
            }

          } catch (Exception $e) {
            errorHandle::errorMsg($e->getMessage());
            return $e->getMessage();
          }
        }

        public function renderSingleRecord($id){
        try {
            $engine    = EngineAPI::singleton();
            $localvars = localvars::getInstance();
            $validate  = new validate;

            if(isnull($id) || !$validate->integer($id)){
                throw new Exception('Id is null or not an integer.  Please try again.');
            }
            else {
                $dataRecord = self::getRecords($id);
                $output = "";
                foreach($dataRecord as $data){
                    $output .= sprintf("<div class='archiveRecord'>
                                            <h2 class='filename'>%s</h2>
                                            <div class='title'>
                                                <strong>Title:</strong>
                                                %s
                                            </div>
                                            <div class='contactInfo'>
                                                <div class='album'>%s</div>
                                                <div class='author'>%s</div>
                                                <div class='track'>%s</div>
                                                <div class='year'>%s</div>
                                                <div class='length'>%s</div>
                                                <div class='lyric'>%s</div>
                                            </div>
                                            <div class='actions'>
                                                <a href='/mediaArchive/edit/%s'> <span class='glyphicon glyphicon-edit'></span> </a>
                                                <a href='/mediaArchive/delete/%s'><span class='glyphicon glyphicon-trash'></span> </a>
                                            </div>
                                        </div>",
                            $data['filename'],
                            $data['title'],
                            $data['album'],
                            $data['author'],
                            $data['track'],
                            $data['year'],
                            $data['length'],
                            $data['lyric'],
                            $data['archiveID'],
                            $data['archiveID']
                    );
                }

                return $output;
            }

        } catch (Exception $e) {
            errorHandle::errorMsg($e->getMessage());
            return $e->getMessage();
        }
    }

        public function verifyTableFiles($path){
          try {
            $engine     = EngineAPI::singleton();
            $localvars  = localvars::getInstance();
            $validate   = new validate;
            $dataRecord = self::getRecords();

            $records    = "";

            foreach($dataRecord as $data){
              if (!file_exists($path . '/' . $data['filename'])) {
                self::deleteRecord($data['archiveID']);
              }
            }
            return true;
          }

          catch (Exception $e) {
            errorHandle::errorMsg($e->getMessage());
            return $e->getMessage();
          }
        }

        public function renderDataTable(){

          self::updateTable();

          try {
            $engine     = EngineAPI::singleton();
            $localvars  = localvars::getInstance();
            $validate   = new validate;
            $dataRecord = self::getRecords();

            $records    = "";

            foreach($dataRecord as $data){
                $records .= sprintf("<tr>
                                        <td>%s</td>
                                        <td>%s</td>
                                        <td>%s</td>
                                        <td>%s</td>
                                        <td><a href='mediaArchive/edit/%s'><span class='glyphicon glyphicon-edit'></span> </a></td>
                                        <td><a href='mediaArchive/confirmDelete/%s'> <span class='glyphicon glyphicon-trash'></span> </a></td>
                                        <td><a href='mediaArchive/play/%s'> <span class='glyphicon glyphicon-play-circle'></span> </a></td>
                                    </tr>",
                        $data['filepath'],
                        $data['filename'],
                        $data['filetype'],
                        $data['length'],
                        $data['archiveID'],
                        $data['archiveID'],
                        $data['archiveID']
                );
            }

            $output     = sprintf("<div class='dataTable table-responsive'>
                                        <table class='table table-striped'>
                                            <thead>
                                                <tr class='info'>
                                                    <th> Filepath </th>
                                                    <th> Filename </th>
                                                    <th> Filetype </th>
                                                    <th> Length </th>
                                                    <th> </th>
                                                    <th> </th>
                                                    <th> </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                %s
                                            </tbody>
                                        </table>
                                    </div>",
                $records
            );

            return $output;

          } catch (Exception $e) {
            errorHandle::errorMsg($e->getMessage());
            return $e->getMessage();
          }
        }

        public function getJSON($id = null){
          $validate = new validate;
          if(!isnull($id) && $validate->integer($id)){
            $data = self::getRecords($id);
          } else {
            $data = self::getRecords();
          }
          return json_encode($data);
        }

        public static function updateTable(){
          // Initialize getID3 engine
          $getID3 = new getID3;

          // add all mp3 files into mediaArchive Table
          $engine    = EngineAPI::singleton();
          $localvars = localvars::getInstance();
          $db        = db::get($localvars->get('dbConnectionName'));
          $path      = "./media/";

          //verify that all files that are in the table actually exists else removes the entry in the table
          self::verifyTableFiles($path);

          // Open a directory, and read its contents and store it results in mediaArchive table
          if(is_dir($path) && ($dh = opendir($path))){
            while (($file = readdir($dh)) !== false) {
              try {
                //if ($file == "index.php" || preg_match('/^\./', $file) || !preg_match('/\.mp3$/', $file)) continue;
                if ($file == "index.php" || preg_match('/^\./', $file)) continue;

                // set full location of file with path
                $FullFileName = $path . $file;

                //check mime type
                $filetype = file_mime_type($FullFileName);

                //analyze file
                $ThisFileInfo = $getID3->analyze($FullFileName);

                //get id3 values from file
                getid3_lib::CopyTagsToComments($ThisFileInfo);

                // set id info from file
                $title = $ThisFileInfo['comments_html']['title'][0];
                $album = $ThisFileInfo['comments_html']['album'][0];
                $author = $ThisFileInfo['comments_html']['artist'][0];
                $track = $ThisFileInfo['comments_html']['track_number'][0];
                $year = $ThisFileInfo['comments_html']['year'][0];

                //get total length of playtime in minutes and seconds
                $length = $ThisFileInfo['playtime_string'];

                // SQL Results

                $sql = "INSERT INTO `mediaArchive` (filepath, filename, filetype, title, album, author, track, length) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $sqlResult = $db->query($sql, array(ltrim($path, '.'), $file, $filetype, $title, $album, $author, $track, $length));

                if (!$sqlResult) {
                  throw new Exception(__METHOD__.' Failed to insert mediaArchive record.');
                }
            }

            catch(Exception $e) {
             errorHandle::errorMsg($e->getMessage());
             return $e->getMessage();
             }
            }

            closedir($dh);
            return true;
          }
        }

        public static function playAudioFile($id = null){
          try {
            $engine    = EngineAPI::singleton();
            $localvars = localvars::getInstance();
            $validate  = new validate;

            if(isnull($id) || !$validate->integer($id)){
              throw new Exception('Id is null or not an integer.  Please try again.');
            }
            else {
              $dataRecord = self::getRecords($id);
              $output = "";

              foreach($dataRecord as $data){
                 $output .= sprintf("<div class='archiveRecord'>
                                        <h2 class='filename'>%s</h2>
                                         <div class='title'>
                                            <strong>Title:</strong>
                                            %s
                                         </div>

                                        <div class='audioControls'>
                                          <audio controls>
                                            <source src='%s%s' type='audio/mp3'>
                                            Your browser does not support the audio element.
                                           </audio>
                                        </div>

                                    </div>",
                        $data['filename'],
                        $data['title'],
                        $data['filepath'],
                        $data['filename'],
                        $data['archiveID']
                      );
                }

              return $output;
            }

          } catch (Exception $e) {
            errorHandle::errorMsg($e->getMessage());
            return $e->getMessage();
          }
        }

        public static function playVideoFile($id = null){
          try {
            $engine    = EngineAPI::singleton();
            $localvars = localvars::getInstance();
            $validate  = new validate;

            if(isnull($id) || !$validate->integer($id)){
              throw new Exception('Id is null or not an integer.  Please try again.');
            }
            else {
              $dataRecord = self::getRecords($id);
              $output = "";

              $FFmpeg = new FFmpeg( '/usr/local/bin/ffmpeg' );
              $FFmpeg->input( '.' . $dataRecord[0]['filepath'] . $dataRecord[0]['filename']);
              $FFmpeg->output( 'new.mp4' , 'mp4' );
              print($FFmpeg->command);
              die();

              foreach($dataRecord as $data){
                $output .= sprintf("<div class='archiveRecord'>
                                    <h2 class='filename'>%s</h2>
                                     <div class='title'>
                                        <strong>Title:</strong>
                                        %s
                                     </div>

                                    <div class='videoControls'>
                                      <video controls>
                                        <source src='%s%s' type='video/mp4'>
                                        Your browser does not support the <code>video</code> element.
                                       </video>
                                    </div>

                                </div>",
                    $data['filename'],
                    $data['title'],
                    $data['filepath'],
                    $data['filename'],
                    $data['archiveID']
                );
              }

              return $output;
            }

          } catch (Exception $e) {
            errorHandle::errorMsg($e->getMessage());
            return $e->getMessage();
          }
        }
    }
?>
