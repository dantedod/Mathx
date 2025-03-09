<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MainController extends Controller
{
  public function home(): View
  {
    return view("home");
  }
  public function generateExercisies(Request $request)
  {
    echo "Gerador de exercicios";
  }
  public function printExercisies()
  {
    echo "Imprimir exercicios no navegador ";
  }
  public function exportExercisies()
  {
    echo "Exportar exercicios para um arquivo de texto";
  }
}