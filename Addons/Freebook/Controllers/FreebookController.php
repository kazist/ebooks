<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ebooks\Addons\Freebook\Controllers;

use Kazist\Controller\AddonController;
use Ebooks\Addons\Freebook\Models\FreebookModel;
use Kazist\KazistFactory;

/**
 * Kazist view class for the application
 *
 * @since  1.0
 */
class FreebookController extends AddonController {

    function indexAction($offset = 0, $limit = 6) {

        $factory = new KazistFactory();
        $model = new FreebookModel;

        $free_ebooks = $model->getFreeEbooks();
        $id = $this->request->request->get('id');

        $data_arr['id'] = $id;
        $data_arr['free_ebooks'] = $free_ebooks;

        $this->html = $this->render('Ebooks:Addons:Freebook:views:freebook.twig', $data_arr);

        $response = $this->response($this->html);



        return $response;
    }

}
