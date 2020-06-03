<?php

namespace Different\Dwfw\Tests\Unit\Models;

use Different\Dwfw\app\Models\Partner;
use Different\Dwfw\Tests\TestCase;

class PartnerTest extends TestCase
{
    /**
     * @test
     * @dataProvider partnerTestDatas
     */
    function it_verifies_partner_contact_name_with_name($expected, $partner_name, $contact_name)
    {
        $this->assertEquals($expected, (new Partner([
            'name' => $partner_name,
            'contact_name' => $contact_name,
        ]))->name_contact_name);
    }

    function partnerTestDatas()
    {
        return [
            ['Méla Béla Bt. - Vezető Viktor', 'Méla Béla Bt.', 'Vezető Viktor'],
            ['Cseles Kft. - Bika Béla', 'Cseles Kft.', 'Bika Béla'],
            ['Zárva Zrt.', 'Zárva Zrt.', ''],
        ];
    }
}
