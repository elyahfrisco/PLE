<?php

namespace App\Console\Commands\Make;

use Illuminate\Support\Str;

trait GeneratorCommandTrait
{
  protected $defaultModelStubePath = 'app\Console\Commands\Make\Stubs\\';

  protected function replaceClass($stub, $name)
  {
    $class = Str::studly(str_replace($this->getNamespace($name) . '\\', '', $name));

    return str_replace('DummyClass', $class, $stub);
  }

  protected function getNameInput()
  {
    $file_name = Str::of(trim($this->argument('name')))->studly();

    if (!Str::endsWith($file_name, $this->type))
      $file_name = $file_name->append($this->type);

    return $file_name;
  }
}
