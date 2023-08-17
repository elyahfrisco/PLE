<?php

namespace App\Console\Commands\Make;

use Illuminate\Console\GeneratorCommand;

class ModelRelationMakeCommand extends GeneratorCommand
{
  use GeneratorCommandTrait;

  protected $signature = 'make:model-relation {name}';

  protected $description = 'Create a new model relation';

  protected $type = 'Relation';

  protected function getStub()
  {
    return base_path($this->defaultModelStubePath . 'relation.stub');
  }

  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace . '\Models\Relations';
  }
}
