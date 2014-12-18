<?php

namespace dnocode\awsddb\ddb\enums;

use Aws\Common\Enum;

class Search extends  Enum  {

  const QUERY="get";
  const SCAN="scan";

}