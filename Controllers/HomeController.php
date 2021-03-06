<?php

namespace Controllers;

use Models\Board;
use Models\Card;
use Models\Liste;
use Models\User;

class HomeController extends Controller {

  // "Convention" Method par défaut d'appel d'un controleur
  public function index()
  {

  }

  //Display user Profile
  public function profile($userId){
      if(!isset($_SESSION)){
          session_start();
      }
      if( isset($_SESSION["id"]) && $_SESSION["id"] === $userId){

          $user = new User();
          $checkUser = $user->findUserById($userId);

          $this->view('profile.php', [
              'user' => $checkUser,
          ]);
      }

      else{
          $this->view('landing.php');
      }

  }

  //Display all boards of an User
  public function dashboard($userId){

      if(!isset($_SESSION)){
          session_start();
      }

      if(isset($_SESSION["id"])) {

          if ($_SESSION["id"] === $userId) {

              $boardManager = new Board();

              $boards = $boardManager->showBoards($userId);

              $listeManager= new Liste();

              $arrayBoards =[];



              foreach($boards as $board){
                  $listes=$listeManager->showListes($board["id"]);

                  array_push($board, $listes);

                  array_push($arrayBoards, $board);
              }

              $boards=$arrayBoards;

              $this->view('dashboard.php', [
                  'boards' => $boards,
                  "message" => "Bienvenu"
              ]);
          } else {
              $this->view('landing.php', [
                  "message" => "Vous êtes connecté"
              ]);
          }
      }

      else{
          $this->view('landing.php');
      }


  }

    // Create Board from Dashboard and redirect to the created board panel
    public function addBoard(){
        if(!isset($_SESSION)){
            session_start();
        }
        if(isset($_SESSION["id"])) {

            if(isset($_POST["title"]) && isset($_POST["description"]) &&
                !empty($_POST["title"])
                && isset($_POST["id"])){

                if($_POST["id"]===$_SESSION["id"]){

                    $board = new Board();
                    $idBoard = $board->addBoard($_POST["title"], $_POST["description"], $_POST["id"]);

                    $newBoard = $board->findBoardById($idBoard);

                    $this->view("board.php", [
                        "board" => $newBoard,
                        "message" => "Tableau crée"
                    ]);
                }
                else{
                    $this->dashboard($_SESSION["id"]);
                }
            }
            else{
                $this->dashboard($_SESSION["id"]);
            }
        }
        else{
            $this->view("connect.php");
        }
    }

    //Display a bord with it's id
    public function displayBoard($idBoard){

        if(!isset($_SESSION)){
            session_start();
        }

        if(isset($_SESSION["id"])) {

            $user = $_SESSION["id"];

            $board = new Board();

            $selectBoard = $board->showBoard($idBoard);

            $listeManager = new Liste();

            $listes= $listeManager->showListes($idBoard);

            $cardManager= new Card();

            $arrayListe =[];

            foreach($listes as $liste){
                $cards=$cardManager->showCards($liste["id"]);
                array_push($liste, $cards);
                array_push($arrayListe, $liste);
            }

            $listes=$arrayListe;


            $this->view("board.php", [
                "board" =>$selectBoard,
                "listes" => $listes
            ]);
        }
    }

    public function deleteBoard($idBoard){

        if(!isset($_SESSION)){
            session_start();
        }

        if(isset($_SESSION["id"])) {

            $user = $_SESSION["id"];

            $board = new Board();

            $selectBoard = $board->deleteBoard($idBoard, $user);

            $this->dashboard($user);
        }
    }

    public function addListe()
    {
        if(!isset($_SESSION)){
            session_start();
        }

        if (isset($_SESSION["id"])) {
            if (isset($_POST["title"]) && isset($_POST["description"]) &&
                !empty($_POST["title"])
                && isset($_POST["id"])) {

                    $liste = new Liste();
                    $liste->addListe($_POST["title"], $_POST["description"], $_POST["id"]);

                    $this->displayBoard($_POST["id"]);
            }
            else{
                $this->displayBoard($_POST["idBoard"]);
            }
        } else {
            $this->view("login.php");
        }
    }

    public function deleteListe($idListe){

        if(!isset($_SESSION)){
            session_start();
        }

        if(isset($_SESSION["id"])) {

            $listeManager= new Liste();
            $liste = $listeManager->findListeById($idListe);

            $board =$liste["id_board"];

            $listeManager->deleteListe($idListe);

            $data["message"]="Liste supprimé";
            $this->displayBoard($board);
        }

    }


    public function addUser(){

        if(!isset($_SESSION)){
            session_start();
        }

        if (isset($_SESSION["id"])) {
            if (isset($_POST["email"])) {
                $user = New User();
                $addUser = $user->findUserByEmail($_POST["email"]);

                if($addUser!=false){

                    $board = new Board();
                    $board->addUser($addUser["id"],$_POST["idBoard"]);

                    $data["message"]="Utilisateur ajouté";
                    $this->displayBoard($_POST["idBoard"]);
                }

                else{
                    $this->displayBoard($_POST["idBoard"]);
                }
            }

            else{
                $this->displayBoard($_POST["idBoard"]);
            }
            
        }
        $this->view("connect");
    }

    public function addCard()
    {
        if(!isset($_SESSION)){
            session_start();
        }

        if (isset($_SESSION["id"])) {
            if (isset($_POST["title"]) && isset($_POST["description"]) &&
                !empty($_POST["title"])
                && isset($_POST["idListe"]) && isset($_POST["idBoard"])) {

                $cardManager = new Card();
                $cardManager->addCard($_POST["title"], $_POST["description"], $_POST["idListe"]);

                $this->displayBoard($_POST["idBoard"]);
            }
            else{
                $this->displayBoard($_POST["idBoard"]);
            }
        } else {
            $this->view("login.php");
        }
    }

    public function deleteCard($idCard){

        if(!isset($_SESSION)){
            session_start();
        }

        if(isset($_SESSION["id"])) {


            $cardManager= new Card();
            $card = $cardManager->findCardById($idCard);

            $listeManager = new Liste();
            $liste = $listeManager->findListeById($card["id_list"]);
            $cardManager->deleteCard($idCard);

            $this->displayBoard($liste["id_board"]);
        }

    }
}
