<?php

    class PageController extends Controller {

        public function index() {
            $this->app->render('Page/index.html.twig');
        }

        public function hello() {
            if(isset($_POST) && $_POST['name'] != '') {
                $this->app->render('Page/hello.html.twig', array(
                    'name' => $_POST['name'],
                ));
            } else {
                $this->app->redirectTo('index');
            }
        }
    }