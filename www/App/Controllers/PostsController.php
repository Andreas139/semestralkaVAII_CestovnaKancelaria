<?php

namespace App\Controllers;

use App\Core\AControllerBase;
use App\Core\Responses\Response;
use App\Models\Like;
use App\Models\Post;
use App\Models\ZapZajazdy;


class PostsController extends AControllerBase
{
    /**
     * Authorize controller actions
     * @param $action
     * @return bool
     */
    public function authorize($action)
    {
        //  return true;
        switch ($action)
        {
            case "delete":
            case "create":
            case "store":
            case "edit":
            case "like":
                return $this->app->getAuth()->isLogged();

        }
        return true;
    }

    public function index(): Response
    {
        // TODO: Implement index() method.
        $posts = Post::getAll();
        return $this->html($posts);
    }

    public function delete(){
        $id = $this->request()->getValue('id');
        $postToDelete = Post::getOne($id);
        if ($postToDelete) {
            $postToDelete->delete();
        }
         return $this->redirect("?c=posts");
    }

    public function store(){
        $id = $this->request()->getValue('id');
        $post = ($id ? Post::getOne($id) : new Post());
        $post->setPoznamka($this->request()->getValue('text'));
        $post->save();
        return $this->redirect('?c=posts');

    }
    public function storeText(){
        $id = $this->request()->getValue('id');
        $post = ($id ? Post::getOne($id) : new Post());

        $post->setPouzivatel($this->request()->getValue('pouzivatel'));
        $post->setNazovZajazdu($this->request()->getValue('nazovZajazdu'));
        
        $post->save();
        return $this->redirect('?c=posts');

    }



    public function create(){

        return $this->html(new Post(),viewName:  'create.form');

    }


    public function edit(){
        $id = $this->request()->getValue('id');
        $postToEdit = Post::getOne($id);
        return $this->html($postToEdit,viewName: 'create.form');

    }

    public function like(){
        $id = $this->request()->getValue('id');
        $postToLike = Post::getOne($id);

        foreach ( $postToLike->getLikes() as $like) {
            if ($like->getUser() == $this->app->getAuth()->getLoggedUserName()){
                $like->delete();
                return $this->redirect('?c=posts');
            }
        }

        $newLike = new Like();
        $newLike->setUser( $this->app->getAuth()->getLoggedUserName());
        $newLike->setPostId($id);
        $newLike->save();
        return $this->redirect('?c=posts');


    }

    /*  public function index()
        {
            $posts = Post::getAll();
            return $this->html($posts);
        }
    */
}