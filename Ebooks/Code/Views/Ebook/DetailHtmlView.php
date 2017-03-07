<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ebooks\Ebooks\Views\Ebook;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazicode\View\Detail\DetailHtmlView as GeneralDetailHtmlView;
use Kazicode\Service\KazistFactory;

/**
 * News HTML view class for the application
 *
 * @since  1.0
 */
class DetailHtmlView extends GeneralDetailHtmlView {

    public function prepare() {

        $item = $this->model->getItem();

        parent::prepare();

        $can_download = $this->model->canDownloadEbook();
        $is_flip_pages = $this->model->checkFlipPages($item);
        $this->model->logEbookViews($item->id);

        if (!$can_download) {
            $this->model->redirectToEbook();
        }

        if ($is_flip_pages) {
            $flip_pages = $this->model->getPagesList();
            $this->renderer->set('flip_pages', $flip_pages);
        }

        $this->renderer->set('is_flip_pages', $is_flip_pages);
    }

}
