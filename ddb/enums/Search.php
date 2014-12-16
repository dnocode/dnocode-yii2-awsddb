<?php

namespace dnocode\awsddb\enums;

use Aws\Common\Enum;

class Search extends  Enum  {

  const QUERY="get";
  const SCAN="scan";

}