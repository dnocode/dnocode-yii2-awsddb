<?php

namespace dnocode\awsddb\ddb\enums;

use Aws\Common\Enum;

class Search extends  Enum  {

  const GET="getItem";
  const QUERY="query";
  const SCAN="scan";

}