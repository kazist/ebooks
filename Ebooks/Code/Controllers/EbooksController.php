<?php

/*
 * This file is part of Kazist Framework.
 * (c) Dedan Irungu <irungudedan@gmail.com>
 *  For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * 
 */

/**
 * Description of EbooksController
 *
 * @author sbc
 */

namespace Ebooks\Ebooks\Code\Controllers;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazist\Controller\BaseController;
use Ebooks\Ebooks\Code\Models\EbooksModel;

class EbooksController extends BaseController {

    public function indexAction() {

        $items = $this->model->getRecords();
        $can_download = $this->model->canDownloadEbook();

        $data_arr['items'] = $items;
        $data_arr['can_download'] = $can_download;

        $this->html = $this->render('Ebooks:Ebooks:Code:views:table.index.twig', $data_arr);

        $response = $this->response($this->html);



        return $response;
    }

    public function detailAction() {

        $this->model = new EbooksModel();

        $view = $this->request->get('view');
        $id = $this->request->get('id');

        if ($view == 'preview') {

            $item = $this->model->getRecord($id);
            $is_flip_pages = $this->model->checkFlipPages($item);
            //  $this->model->logEbookViews($item->id);

            if ($is_flip_pages) {
                $flip_pages = $this->model->getPagesList();
                $data_arr['flip_pages'] = $flip_pages;
            }

            $data_arr['item'] = $item;
            $data_arr['is_flip_pages'] = $is_flip_pages;

            $this->html = $this->render('Ebooks:Ebooks:Code:views:preview.index.twig', $data_arr);

            print_r($this->html);
            exit;
        } else {
            return parent::detailAction($id);
        }
    }

    public function downloadAction() {

        $download_status = $this->model->downloadEbook();

        if ($download_status) {
            exit;
        } else {
            return $this->redirectToRoute('ebooks.ebooks');
        }
    }

    public function cronarchiveuserebookAction() {

        $this->model = new EbooksModel();
        $this->model->archiveEbooks();

        return $this->json($data);
    }

    public function cronadduserebookAction() {

        $this->model = new EbooksModel();
        $this->model->addUserEbooks();
        return $this->json($data);
    }

}
