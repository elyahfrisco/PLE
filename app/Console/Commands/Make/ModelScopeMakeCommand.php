<?php

namespace App\Console\Commands\Make;

use Illuminate\Console\GeneratorCommand;

class ModelScopeMakeCommand extends GeneratorCommand
{
  use GeneratorCommandTrait;

  protected $signature = 'make:model-scope {name}';

  protected $description = 'Create a new model scope';

  protected $type = 'Scope';

  protected function getStub()
  {
    return base_path($this->defaultModelStubePath . 'scope.stub');
  }

  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace . '\Models\Scopes';
  }
}
