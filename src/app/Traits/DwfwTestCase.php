<?php


namespace Different\Dwfw\app\Traits;


use Illuminate\Support\Arr;
use Exception;

trait DwfwTestCase
{
    protected function it_should_have_required_fields($api_url, $required_fields, $api_data, $token = null)
    {
        foreach ($required_fields as $required_field) {
            if (!isset($api_data[$required_field])) {
                throw new Exception('Unset required field in api data');
            }
            $response = $this
                ->postJson(
                    $api_url,
                    Arr::except($api_data, [$required_field]),
                    [
                        'Accept' => 'application/json',
                        'Authorization' => $token ? 'Bearer ' . $token : '',
                    ]
                )
                ->decodeResponseJson();
            $this->assertTrue($response['error']);
            $this->assertArrayHasKey($required_field, $response['message']);
            $this->assertEquals(1, sizeof($response['message']));  // https://bug.different.hu/view.php?id=6636
        }
    }

    protected function assertResponseForError($response, string $property): void
    {
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey($property, $response['message']);
        $this->assertEquals(1, sizeof($response['message']));  // https://bug.different.hu/view.php?id=6636
    }


}
