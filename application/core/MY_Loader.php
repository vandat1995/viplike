<?php

class MY_Loader extends CI_Loader 
{
    public function template($template_name, $vars = array(), $return = FALSE)
    {
        if($return)
        {
            $content  = $this->view('templates/header', $vars, $return);
            $content .= $this->view('templates/sidebar', $vars, $return);
            $content .= $this->view('contents/'.$template_name, $vars, $return);
            $content .= $this->view('templates/footer', $vars, $return);
            return $content;
        }     
        else
        {
            $this->view('templates/header', $vars);
            $this->view('templates/sidebar', $vars);
            $this->view('templates/navbar', $vars);
            $this->view('contents/'.$template_name, $vars);
            $this->view('templates/footer', $vars);
        }
    }

    public function error_template($template_name, $vars = array(), $return = FALSE)
    {
        if($return)
        {
            $content  = $this->view('templates/header', $vars, $return);
            $content .= $this->view('errors/html/'.$template_name, $vars, $return);
            $content .= $this->view('templates/footer', $vars, $return);
            return $content;
        }
        else
        {
            $this->view('templates/header', $vars);
            $this->view('errors/html/'.$template_name, $vars);
            $this->view('templates/footer', $vars);
        }
    }
}
