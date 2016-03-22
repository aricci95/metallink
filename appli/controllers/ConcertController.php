<?php

require_once ROOT_DIR . '/appli/controllers/SearchController.php';

class ConcertController extends SearchController
{
    protected $_type = SEARCH_TYPE_CONCERT;

    protected $_searchParams = array(
        'search_distance',
        'search_keyword',
        'search_style',
    );

    public function render()
    {
        $this->view->addJS(JS_MODAL);
        $this->view->styles = $this->model->style->find(array('style_id', 'style_libel'));
        parent::render();
    }

    public function renderMore()
    {
        echo "
        <script>
            $('.popup').magnificPopup({
                type: 'ajax',
                alignTop: true,
                overflowY: 'scroll'
            });
        </script>
        ";

        parent::renderMore();
    }
}
