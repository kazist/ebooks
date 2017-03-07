<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ebooks\Ebooks\Views\Ebook;

defined('KAZIST') or exit('Not Kazist Framework');

use Kazicode\View\Table\TableHtmlView as GeneralTableHtmlView;

/**
 * News HTML view class for the application
 *
 * @since  1.0
 */
class TableHtmlView extends GeneralTableHtmlView {

    public function prepare() {


        parent::prepare();

        $can_download = $this->model->canDownloadEbook();

        $this->renderer->set('can_download', $can_download);
    }

}
