<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ebooks\Addons\Ebooks\Controllers;

use Kazist\Controller\AddonController;
use Ebooks\Addons\Ebooks\Models\EbooksModel;

/**
 * Kazist view class for the application
 *
 * @since  1.0
 */
class EbooksController extends AddonController {

    function indexAction($offset = 0, $limit = 6) {

        $model = new EbooksModel;

        $ebooks = $model->getEbooks();

        $data_arr['ebooks'] = $ebooks;

        $this->html = $this->render('Ebooks:Addons:Ebooks:views:ebooks.twig', $data_arr);

        $response = $this->response($this->html);



        return $response;
    }

}
