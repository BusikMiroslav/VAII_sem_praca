<?php

namespace App\Controllers;

use App\Config\Configuration;
use App\Core\AControllerBase;
use App\Core\Responses\Response;
use App\Models\Osoba;

/**
 * Class AuthController
 * Controller for authentication actions
 * @package App\Controllers
 */
class AuthController extends AControllerBase
{
    /**
     *
     * @return \App\Core\Responses\RedirectResponse|\App\Core\Responses\Response
     */
    public function index(): Response
    {
        return $this->redirect(Configuration::LOGIN_URL);
    }

    /**
     * Login a user
     * @return \App\Core\Responses\RedirectResponse|\App\Core\Responses\ViewResponse
     */
    public function login(): Response
    {
        $formData = $this->app->getRequest()->getPost();

        if(isset($formData['submit'])) {
            $osoba = Osoba::getOneByEmail($this->request()->getValue('email'));
            if(Osoba::getOneByEmail($this->request()->getValue('email')) != null) {
                if($this->request()->getValue('password') == $osoba->getHeslo()) {
                    $this->app->getAuth()->login($osoba->getEmail(), $osoba->getHeslo());
                    return $this->redirect("?c=home&a=cart");
                } else {
                    echo '<script>alert("Zlé prihlasovacie údaje!")</script>';
                }
            }
        }
        return $this->html();
    }

    /**
     * Logout a user
     * @return \App\Core\Responses\ViewResponse
     */
    public function logout(): Response
    {
        $this->app->getAuth()->logout();
        return $this->redirect("?c=auth&a=login");
    }

    public function regist() {
        return $this->html(new Osoba(), viewName: 'registration');
    }


    public function registration(): Response
    {
        //$id = $this->request()->getValue('id');
        if(Osoba::getOneByEmail($this->request()->getValue('email')) != null) {
            echo '<script>alert("Email už existuje!")</script>';
            return $this->html(viewName: 'registration');
        } else {
            $osoba = new Osoba();
            $osoba->setMeno($this->request()->getValue('meno'));
            $osoba->setPriezvisko($this->request()->getValue('priezvisko'));
            $osoba->setTelefon($this->request()->getValue('telefon'));
            $osoba->setEmail($this->request()->getValue('email'));
            $osoba->setHeslo($this->request()->getValue('heslo'));

            $osoba->save();

            return $this->redirect("?c=auth&a=login");
        }
    }

    /**
     * Zabudnuté heslo
     * @return \App\Core\Responses\ViewResponse
     */
    public function forgpassw() : Response
    {
        $formData = $this->app->getRequest()->getPost();
        $osoba = Osoba::getOneByEmail($this->request()->getValue('email'));
        if(isset($formData['submit'])) {
            if(Osoba::getOneByEmail($this->request()->getValue('email')) != null) {
                $osoba->setHeslo($this->request()->getValue('heslo'));
                $osoba->save();
                return $this->redirect("?c=auth&a=login");
            } else {
                echo '<script>alert("Email neexistuje!")</script>';
            }
        }

        return $this->html();
    }
}