<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the BSD-style license found in the
 * LICENSE file in the root directory of this source tree. An additional grant
 * of patent rights can be found in the PATENTS file in the same directory.
 */
namespace Facebook\ShipIt;

final class ShipItPullPhase extends ShipItPhase {
  public function __construct(
    private ShipItRepoSide $side,
  ) {}

  <<__Override>>
  protected function isProjectSpecific(): bool {
    return false;
  }

  <<__Override>>
  public function getReadableName(): string {
    return 'Pull '.$this->side.' repository';
  }

  <<__Override>>
  public function getCLIArguments(): ImmVector<ShipItCLIArgument> {
    $skip_arg = shape(
      'long_name' => 'skip-'.$this->side.'-pull',
      'description' => "Don't pull the ".$this->side." repository",
      'write' => $_ ==> $this->skip(),
    );

    if ($this->side === ShipItRepoSide::SOURCE) {
      return ImmVector {
        $skip_arg,
        shape(
          'long_name' => 'skip-src-pull',
          'replacement' => 'skip-source-pull',
        ),
      };
    } else {
      return ImmVector {
        $skip_arg,
        shape(
          'long_name' => 'skip-dest-pull',
          'replacement' => 'skip-destination-pull',
        ),
      };
    }
  }

  <<__Override>>
  protected function runImpl(
    ShipItBaseConfig $config,
  ): void {
    switch ($this->side) {
      case ShipItRepoSide::SOURCE:
        $local_path = $config->getSourcePath();
        $branch = $config->getSourceBranch();
        break;
      case ShipItRepoSide::DESTINATION:
        $local_path = $config->getDestinationPath();
        $branch = $config->getDestinationBranch();
        break;
    }
    ShipItRepo::open($local_path, $branch)->pull();
  }
}
