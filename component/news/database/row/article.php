<?php
/**
 * Belgian Police Web Platform - News Component
 *
 * @copyright	Copyright (C) 2012 - 2013 Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/belgianpolice/internet-platform
 */



namespace Nooku\Component\News;
use Nooku\Library;

class DatabaseRowArticle extends Library\DatabaseRowTable
{
    public function __get($column)
    {
        if($column == 'text' && !isset($this->_data['text'])) {
            $this->_data['text'] = $this->fulltext ? $this->introtext.'<hr id="system-readmore" />'.$this->fulltext : $this->introtext;
        }

        if($column == 'blocks' && !isset($this->_data['blocks'])) {
            $this->_data['blocks'] = json_decode($this->introtext);
        }

        return parent::__get($column);
    }

    public function save()
    {
        //Set the introtext and the full text
        $text    = str_replace('<br>', '<br />', $this->text);
        $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';

        // If created_on is modified then convert it to GMT/UTC
        if ($this->isModified('created_on') && !$this->isNew())
        {
            $this->created_on = gmdate('Y-m-d H:i:s', strtotime($this->created_on));
        }

        /*
         *  Next Generation Editor
         */
        if($this->content) {
            include('simple_html_dom.php');

            $html = str_get_html($this->content);
            $data = array();
            $i = '0';
            foreach($html->find('div.block') as $block) {
                $i++;

                foreach($block->find('h2') as $h2) {
                    $data[$i]['heading'] = $h2->innertext;
                }

                foreach($block->find('p') as $p) {
                    $data[$i]['text'] = $p->innertext;
                }

                if(count($block->find('img'))) {
                    foreach($block->find('img') as $img) {
                        $data[$i]['attachments_attachment_id'] = $img->getAttribute('data-id');
                    }
                } else {
                    $data[$i]['attachments_attachment_id'] = '0';
                }
            }

            $this->introtext = json_encode($data);
        }

        //Add publish_on date if not set
        if(empty($this->publish_on))
        {
            $this->publish_on = gmdate('Y-m-d H:i:s');
        }

        // Unpublish article if publish_on date is set to future
        if($this->publish_on > gmdate('Y-m-d H:i:s'))
        {
            $this->published = '0';
        }

        $result   = parent::save();

        return $result;
    }
}