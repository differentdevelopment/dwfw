<?php

namespace Different\Dwfw\app\Observers;

use Different\Dwfw\app\Models\Log;

class PartnerObserver extends Observer
{
    protected string $ENTITY_TYPE = Log::ET_PARTNER;

    /**
     * @param Partner $partner
     * @return string
     */
    public function getData($partner)
    {
        return $partner;
    }
}
