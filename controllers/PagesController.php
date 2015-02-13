<?php

    class PagesController extends Controller {

        public function index() {
            $this->app->render('Pages/index.html.twig');
        }

        public function hello($name) {
            $this->app->render('Pages/hello.html.twig', array(
                'name' => $name,
            ));
        }
    }