<?php

namespace dnocode\awsddb\ddb\enums;

use Aws\Common\Enum;

class Filter extends  Enum  {

  const ScanFilter="ScanFilter";
  const KeyConditions="KeyConditions";
  const Query="QueryFilter";

}