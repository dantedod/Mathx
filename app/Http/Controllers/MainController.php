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
    //check if exercises are in session
    if (!session()->has('exercises')) {
      return redirect()->route('home');
    };

    $exercises = session('exercises');

    echo ' <pre>';
    echo ' <h1>Exercicios de Matematica (' . env('APP_NAME') . ') </h1>';
    echo '<hr>';

    foreach ($exercises as $exercise) {
      echo '<h2><small>' . $exercise['exercise_number']  . ' >> </small>' . $exercise['exercise'] . '</h2>';
    }

    //solution
    echo '<hr>';
    echo '<small> Solucoes</small><br>';

    foreach ($exercises as $exercise) {
      echo '<small>'  . $exercise['exercise_number']  . ' >> ' . $exercise['exercise'] . $exercise['sollution'] . '</small><br>';
    }
  }
  public function exportExercisies()
  {
    //here check if exists exercises in session
    if (!session()->has('exercises')) {
      return redirect()->route('home');
    };

    //recebe os exercicios
    $exercises = session('exercises');

    //create file to download with exercises
    $filename = 'exercises_' . env('APP_NAME') . '_' . date('YmdHis') . '.txt';

    $content = '';
    foreach ($exercises as $exercise) {
      $content .= $exercise['exercise_number'] . ' > ' . $exercise['exercise'] . "\n";
    }

    //solutions
    $content .= "\n";
    $content .= 'Solucoes' .  "\n" . str_repeat('-', 20) . "\n";
    foreach ($exercises as $exercise) {
      $content .= $exercise['exercise_number'] . ' = ' . $exercise['sollution'] . "\n";
    }

    return response($content)
      ->header('Content-Type', 'text/plain')
      ->header('Content-Disposition', 'attachment; filename= "' . $filename . '"');
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
        $exercise = "$number1 + $number2 = ";
        $sollution = $number1 + $number2;
        break;
      case 'subtraction':
        $exercise = "$number1 - $number2 = ";
        $sollution = $number1 - $number2;
        break;
      case 'multiplication':
        $exercise = "$number1 X $number2 = ";
        $sollution = $number1 * $number2;
        break;
      case 'division':
        //evitar divisao com 0

        if ($number2 == 0) {
          $number2 = 1;
        };
        $exercise = "$number1 : $number2 = ";
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
        'exercise_number' => str_pad($index, 2, '0', STR_PAD_LEFT),
        'exercise' => $exercise,
        'sollution' => " $sollution "
      ];
  }
}
