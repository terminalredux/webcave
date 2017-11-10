<?php
namespace App\Controllers;

use Libs\Base\Controller;
use Libs\AccessControl\AccessControl;

class FileController extends Controller
{
  /**
   * @inheritdoc
   */
  public function __construct() {
    AccessControl::onlyForLogged();
    parent::__construct();
  }

  public function actionIndex() {
    $this->view->render('file/index');
  }

  public function actionAdd() {
    if ($this->isFile()) {
      $file = $_FILES['file'];
      if ($file) {
        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];

        $fileExt = explode('.', $fileName);
        $fileExt = strtolower(end($fileExt));

        $allowedExt = ['png', 'jpg', 'jpeg'];

        if (in_array($fileExt, $allowedExt)) {
          if ($fileError === 0) {
            if ($fileSize <= 2097152) {
              $fileNameNew = uniqid('', true) . '.' . $fileExt;
              $fileDestination = 'web/uploaded_files/' . $fileNameNew;

              if (move_uploaded_file($fileTmp, $fileDestination)) {
                // infomracja o sukcesie
                $this->view->render('file/index');
              } else {
                //obsługa błędu na wypadek niepowodzenia uploadu pliku na serwer
              }
            }
          }
        } else {
          //obsługa błędu gdy plik nie jest akceptowalnego rozszerzenia
        }
      } else {
        //obsługa błędu gdy nie przesłano pliku z pola 'file'
      }
    } else {
      $this->view->render('file/add');
    }
  }
}
