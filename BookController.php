<?php

use Phalcon\Mvc\Model;
use Phalcon\Filter;
use Phalcon\Mvc\Model\Transaction\Manager;
use Phalcon\Db\Adapter\Pdo;
//use Phalcon\Mvc\Model\Transaction\Failed as TxFailed;
//use Phalcon\Http\Message\UploadedFile;
//use Phalcon\Http\Message\Stream;
//use Phalcon\Http\Request\File;

class BookController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Book');
        $this->auth = $this->session->get('auth');
        $this->filter = new Filter();
        parent::initialize();
    }

    public function orderAction(){

    }

    public function uploadAction()
    {
        // Check if the user has uploaded files
        if ($this->request->hasFiles()) {
            $files = $this->request->getUploadedFiles();
            $filename = $this->request->getName('filename');
            $filetype = $this->request->getType('filetype');
            $filesize = $this->request->getSize('filesize');

            // Print the real file names and their sizes
            foreach ($files as $file) {

                $book = new Book();
                $book->filename = $filename;
                $book->filetype = $filetype;
                $book->filesize = $filesize;

                if($file->save()){

                    echo $file->getName(), " ", $file->getSize(), "\n";
                    $file->moveTo(
                        'uploads/' . $file->getName()
                    );

                    return $this->response->redirect('book/index');

                } else {
                    $errors = $file->getMessages();

                    foreach ($errors as $error) {
                        $this->flashSession->error($error->getMessage());
                    }
                }
            }
        }
        else {
            $this->flash->error('There is no file to upload');
        }
    }

    public function deleteAction($id)
    {
        $postedBook = Book::findFirstById($id);
        $filesForTheBook = Filesbook::findFirstByBookId($postedBook->id);

        $files  = array();          //copy the name of properties for the file
        $this->db->begin();

        foreach ($filesForTheBook as $fBook )
        {
            if(!$filesForTheBook->delete()){
                $this->db->rollback();

                $errors = $filesForTheBook->getMessages();

                foreach ($errors as $error) {
                    $this->flashSession->error($error->getMessages());
                }
                return;
            }
            else {
                $files[] = $fBook->filename;
            }
        }

        if(!$postedBook->delete()){

            $this->db->rollback();

            $errors = $postedBook->getMessages();

            foreach ($errors as $error) {
                $this->flashSession->error($error->getMessages());
            }
            return;
        }

        $this->db->commit();

        foreach ($files as $filename)
        {

            $path = "uploads/";
            unlink($path . $filename);

        }

        $this->flash->success('You successfuly deleted this book!');
        return $this->response->redirect('book/index');
    }


    public function createAction()
    {
        if ($this->request->isPost()) {

            //transaction beginning
            $this->db->begin();

            $bookname = $this->request->getPost('bookname');
            $authorname = $this->request->getPost('authorname');
            $publisher = $this->request->getPost('publisher');
            $category = $this->request->getPost('category');
            $submit = $this->request->getPost('submit');

            if (isset($submit)) {
                $book = new Book();
                $book->bookname = $bookname;
                $book->authorname = $authorname;
                $book->publisher = $publisher;
                $book->category = $category;

                if($this->request->hasFiles() && $book->save()){
                    $files = $this->request->getUploadedFiles();

                    foreach ($files as $file) {
                        $fName  = $file->getName();
                        $fType  = $file->getType();
                        $fsize  = $file->getSize();

                        if(!$file->isUploadedFile())
                        {
                            $this->flashSession->error("You also have to upload a file!");
                            return $this->response->redirect('book/index');
                        }
                        $fileForBook    = new Filesbook();
                        $fileForBook->book_id = $book->id;
                        $fileForBook->filename = $fName;
                        $fileForBook->filetype = $fType;
                        $fileForBook->filesize = $fsize;
                        $movieFile = $file->moveTo('uploads/' . $fName);

                        if(!$fileForBook->create()) {

//                            var_dump($fileForBook->getMessages());
//                            exit;
                            //the model failed to save, so rollback the transaction
                            $this->db->rollback();
                            $this->flash->error('Could not save file information in DB');
                            return $this->response->redirect('book/index');
                        }
                    }

                    $this->db->commit();

                    $this->flash->success('You successfuly added new book information');
                    return $this->response->redirect('book/index');
                } else {
                    $errors = $book->getMessages();

                    foreach ($errors as $error) {
                        $this->flashSession->error($error->getMessages());
                    }

                    $this->view->flash = $this->flash;
                }
            }

            $this->view->books = $this->getAllBooks();
        }
    }

    public function updateAction($id)
    {
        $book = Book::findFirstById($id);
        $this->view->book = $book;

        if($this->request->isPost())
        {
            $this->db->begin();

            $bookname = $this->request->getPost('bookname');
            $authorname = $this->request->getPost('authorname');
            $publisher = $this->request->getPost('publisher');
            $category = $this->request->getPost('category');

            if($book)
            {
                $book->bookname = $bookname;
                $book->authorname = $authorname;
                $book->publisher = $publisher;
                $book->category = $category;

                if(!$book->update() && !$this->request->hasFiles())
                {
                    $errors = $book->getMessages();

                    foreach ($errors as $error) {
                        $this->flashSession->error($error->getMessages());
                    }

                    return $this->response->redirect('book/index');

                } else {
                    // Check if the user has uploaded uploadedFiles
                    $uploadedFiles = $this->request->getUploadedFiles();

                    // Print the real file names and their sizes
                    foreach ($uploadedFiles as $file)
                    {
                        $fName  = $file->getName();
                        $fType  = $file->getType();
                        $fSize  = $file->getSize();

                        if(!$file->isUploadedFile())
                        {
                            continue;
                        }

//                        if(empty($fName)) {
//                            break;
//                        }

                        $filebook = Filesbook::findFirstByBookId($book->id);

                        //try to delete images from folder, using unlink method
                        $path = "uploads/";
                        unlink($path . $filebook->filename);

                        if(!$filebook) {
                            $filebook = new Filesbook;
                        }

                        //data from db
                        $filebook->book_id = $book->id;
                        $filebook->filename = $fName;
                        $filebook->filetype = $fType;
                        $filebook->filesize = $fSize;
                        $movefile=$file->moveTo('uploads/' . $fName);

                        if(!$filebook->save())
                        {

                        $this->db->rollback();

                        $errors = $book->getMessages();
                        foreach ($errors as $error) {
                            $this->flashSession->error($error->getMessages());
                        }

                        return $this->response->redirect('book/index');

                        }
                    }

                    $this->db->commit();

                    $this->flash->success('You successfuly added new book information');
                    return $this->response->redirect('book/index');

                }
        }

        return $this->response->redirect('book/index');
    }
}

    public function indexAction()
    {
        /* the fields */
        $bookname = $this->request->getPost('bookname');
        $authorname = $this->request->getPost('authorname');
        $publisher = $this->request->getPost('publisher');
        $category = $this->request->getPost('category');
        $submit = $this->request->getPost('submit');

        if (isset($submit)) {

            $book = new Book();
            $book->date_added = time();
            $book->bookname = $bookname;
            $book->authorname = $authorname;
            $book->publisher = $publisher;
            $book->category = $category;

            if ($book->save()) {

                $this->flash->success('You successfuly added new book information');
            } else {

                $errors = $book->getMessages();

                foreach ($errors as $error) {
                    $this->flash->error($error->getMessage());
                }
            }
        }

        $this->view->flash = $this->flash;
        $this->view->books = $this->getAllBooks();
    }

//    private function getAllFiles()
//    {
//        $files = Files::find([
//            'order' => 'id DESC'
//        ]);
//
//        return $files;
//    }

    private function getAllBooks()
    {
        $books = Book::find([
            'order' => 'id DESC'
        ]);
        return $books;
    }
}


//1st--not ready
//public function updateAction($id)
//{
//    $book = Book::findFirstById($id);
//    $this->view->book = $book;
//
//
//    if($this->request->isPost())
//    {
//        // $this->db->begin();
//
//        $bookname = $this->request->getPost('bookname');
//        $authorname = $this->request->getPost('authorname');
//        $publisher = $this->request->getPost('publisher');
//        $category = $this->request->getPost('category');
//
//        if($book){
//            $book->bookname = $bookname;
//            $book->authorname = $authorname;
//            $book->publisher = $publisher;
//            $book->category = $category;
//
//
//            // Check if the user has uploaded files
//            if ($this->request->hasFiles()) {
//                $files = $this->request->getUploadedFiles();
//
//                // Print the real file names and their sizes
//                foreach ($files as $file) {
//                    $fName  = $file->getName();
//                    $fType  = $file->getType();
//                    $fSize  = $file->getSize();
//
//                    $file->book_id = $book->id;
//                    $file->filename = $fName;
//                    $file->filetype = $fType;
//                    $file->filesize = $fSize;
////                        var_dump($file); exit;
//                    $movefile=$file->moveTo('uploads/' . $fName);
//
//                    if(!$file->update()) {
////                            exit();
////                            var_dump($file->getMessages());
////                            exit;
//                        //$this->db->rollback();
//                        $this->flash->error('Could not update file information in DB');
//                        return $this->response->redirect('book/index');
//                    }
//                }
//                //$this->db->commit();
//
//                $this->flash->success('You successfuly added new book information');
//                return $this->response->redirect('book/index');
//            } else {
//
//                $errors = $book->getMessages();
//
//                foreach ($errors as $error) {
//                    $this->flashSession->error($error->getMessage());
//                }
//            }
//        }
//
//        return $this->response->redirect('book/index');
//    }
//
//}

////////////2nd almost///////////////////////////////////////////////////////////////////
//public function updateAction($id)
//{
//    $book = Book::findFirstById($id);
//    $this->view->book = $book;
//
//    if($this->request->isPost())
//    {
//        $this->db->begin();
//
//        $bookname = $this->request->getPost('bookname');
//        $authorname = $this->request->getPost('authorname');
//        $publisher = $this->request->getPost('publisher');
//        $category = $this->request->getPost('category');
//
//        if($book)
//        {
//            $book->bookname = $bookname;
//            $book->authorname = $authorname;
//            $book->publisher = $publisher;
//            $book->category = $category;
//
//            if(!$book->update())
//            {
//                $errors = $book->getMessages();
//
//                foreach ($errors as $error) {
//                    $this->flashSession->error($error->getMessage());
//                }
//
//                return $this->response->redirect('book/index');
//
//            } else {
//                // Check if the user has uploaded files
//                if ($this->request->hasFiles()) {
//                    $files = $this->request->getUploadedFiles();
//
//                    // Print the real file names and their sizes
//                    foreach ($files as $file)
//                    {
//                        $fName  = $file->getName();
//                        $fType  = $file->getType();
//                        $fSize  = $file->getSize();
//
//                        if(empty($fName)) {
//                            break;
//                        }
//
//                        $filebook = Filesbook::findFirstByBookId($book->id);
//
//                        if(!$filebook) {
//                            $filebook = new Filesbook;
//                        }
//                        //data from db
//                        $filebook->book_id = $book->id;
//                        $filebook->filename = $fName;
//                        $filebook->filetype = $fType;
//                        $filebook->filesize = $fSize;
//                        $movefile=$file->moveTo('uploads/' . $fName);
//
////                            $filebook->save();
//
//                        if(!$filebook->save())
//                        {
//
//                            $this->db->rollback();
//
////                                $this->flash->error('Could not save file information in DB');
//
//                            $errors = $book->getMessages();
//                            foreach ($errors as $error) {
//                                $this->flashSession->error($error->getMessage());
//                            }
//
//                            return $this->response->redirect('book/index');
//
//                        }
////                            var_dump($files);
////                            $fileUnlink = unlink("uploads/".$fName);
////                            var_dump($fileUnlink);
////                            exit;
////                            $fileUnlink = unlink($files->filename);
//                    }
//
//                    $this->db->commit();
//
//                    $this->flash->success('You successfuly added new book information');
//                    return $this->response->redirect('book/index');
//                }
//
//                $this->db->commit();
//            }
//        }
//
//        return $this->response->redirect('book/index');
//    }
//}

///  it is work well!!!
//public function updateAction($id)
//{
//    $book = Book::findFirstById($id);
//    $this->view->book = $book;
//
//    if($this->request->isPost())
//    {
//        $this->db->begin();
//
//        $bookname = $this->request->getPost('bookname');
//        $authorname = $this->request->getPost('authorname');
//        $publisher = $this->request->getPost('publisher');
//        $category = $this->request->getPost('category');
//
//        if($book)
//        {
//            $book->bookname = $bookname;
//            $book->authorname = $authorname;
//            $book->publisher = $publisher;
//            $book->category = $category;
//
//            if(!$book->update() && !$this->request->hasFiles())
//            {
//                $errors = $book->getMessages();
//
//                foreach ($errors as $error) {
//                    $this->flashSession->error($error->getMessages());
//                }
//
//                return $this->response->redirect('book/index');
//
//            } else {
//                // Check if the user has uploaded uploadedFiles
//                $uploadedFiles = $this->request->getUploadedFiles();
//
//                // Print the real file names and their sizes
//                foreach ($uploadedFiles as $file)
//                {
//                    $fName  = $file->getName();
//                    $fType  = $file->getType();
//                    $fSize  = $file->getSize();
//
//                    if(! $file->isUploadedFile())
//                    {
//                        $this->flashSession->error("You also have to upload a file!");
//                        return $this->response->redirect('book/index');
//                        //continue;
//                    }
//
////                        if(empty($fName)) {
////                            break;
////                        }
//
//                    $filebook = Filesbook::findFirstByBookId($book->id);
//
//                    if(!$filebook) {
//                        $filebook = new Filesbook;
//                    }
//                    //data from db
//                    $filebook->book_id = $book->id;
//                    $filebook->filename = $fName;
//                    $filebook->filetype = $fType;
//                    $filebook->filesize = $fSize;
//                    $movefile=$file->moveTo('uploads/' . $fName);
//
//                    if(!$filebook->save())
//                    {
//
//                        $this->db->rollback();
//
//                        $errors = $book->getMessages();
//                        foreach ($errors as $error) {
//                            $this->flashSession->error($error->getMessages());
//                        }
//
//                        return $this->response->redirect('book/index');
//
//                    }
//                }
//
//                $this->db->commit();
//
//                $this->flash->success('You successfuly added new book information');
//                return $this->response->redirect('book/index');
//
//            }
//        }
//
//        return $this->response->redirect('book/index');
//    }
//}

//////////////////////////////////////
//            $book->assign(
//                array(
//                  'bookname'=>$this->request->getPost('bookname','striptags'),
//                  'authorname'=>$this->request->getPost('authorname','striptags'),
//                  'publisher'=>$this->request->getPost('publisher','striptags'),
//                  'category'=>$this->request->getPost('category','striptags'),
//                ));
//
//            //delete the entire contents of the related table Files:
//            Filesbook::find('book_id IN ('.$id.')')->delete();
//
//            $postedBook = $this->request->getPost('book');
//            $book       = array();
//            foreach ($postedBook as $key=> $value) {
//                $book[$key] = new Filesbook();
//                $book[$key]->content = $value;
//            }
//            $book->Filesbook=$postedBook;
//            if(!$book-save()){
//            $this->flash->error($book->getMessages());
//            } else {
//            $this->flash->success("Book was updated successfully");
//            }

