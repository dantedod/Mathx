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
  public function generateExercisies(Request $request): View
  {
    //validacao do form
    $request->validate([
      'check_sum' => 'required_without_all:check_subtraction,check_multiplication,check_division',
      'check_subtraction' => 'required_without_all:check_sum,check_multiplication,check_division',
      'check_multiplication' => 'required_without_all:check_subtraction,check_sum,check_division',
      'check_division' => 'required_without_all:check_subtraction,check_multiplication,check_sum',
      'number_one' => 'required|integer|min:0|max:999',
      'number_two' => 'required|integer|min:0|max:999',
      'number_exercises' => 'required|integer|min:5|max:50',
      'number_one' => 'lt:number_two'
    ]);

    //get selected operations
    $opreations = [];
    if ($request->check_sum) {
      $opreations[] = 'sum';
    }
    if ($request->check_subtraction) {
      $opreations[] = 'subtraction';
    }
    if ($request->check_multiplication) {
      $opreations[] = 'multiplication';
    }
    if ($request->check_division) {
      $opreations[] = 'division';
    }


    //get numbers(min and max)
    $min = $request->number_one;
    $max = $request->number_two;

    //get number of exercisies
    $numberExcercisies = $request->number_exercises;

    //generate exercisies
    $exercisies = [];
    for ($index = 1; $index <= $numberExcercisies; $index++) {
      $exercisies[] = $this->generateExercisie($index, $opreations, $min, $max,);
    }

    //place exercises in session
    // $request->session()->put('exercises', $exercisies);
    //outra forma de botar na sessao
    session(['exercises' => $exercisies]);

    return view('operations', ['exercisies' => $exercisies]);
  }
  public function printExercisies()
  {
    echo "Imprimir exercicios no navegador ";
  }
  public function exportExercisies()
  {
    echo "Exportar exercicios para um arquivo de texto";
  }
  private function generateExercisie($index, $opreations, $min, $max): array
  {
    $opreation = $opreations[array_rand($opreations)];
    $number1 = rand($min, $max);
    $number2 = rand($min, $max);

    $exercise = '';
    $sollution = '';

    switch ($opreation) {
      case 'sum':
        $exercise = "$number1 + $number2 =";
        $sollution = $number1 + $number2;
        break;
      case 'subtraction':
        $exercise = "$number1 - $number2 =";
        $sollution = $number1 - $number2;
        break;
      case 'multiplication':
        $exercise = "$number1 X $number2 =";
        $sollution = $number1 * $number2;
        break;
      case 'division':
        //evitar divisao com 0

        if ($number2 == 0) {
          $number2 = 1;
        };
        $exercise = "$number1 : $number2 =";
        $sollution = $number1 / $number2;
        break;
    }

    //if $sollution eh um numero com casas decimais, arredondar para 2 casas decimais
    if (is_float($sollution)) {
      $sollution = round($sollution, 2);
    }
    return
      [
        'operation' => $opreation,
        'exercise_number' => $index,
        'exercise' => $exercise,
        'sollution' => "$exercise $sollution "
      ];
  }
}
