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
        return $this->implodeData([
            $partner->name,
            $partner->contact_name,
            $partner->contact_phone,
            $partner->contact_email,
        ]);
    }
}
