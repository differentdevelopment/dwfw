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
                throw new Exception('Unset required field in api data: ' . $required_field);
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
            $this->assertTrue($response['error'], 'API response does not report error when missing ' . $required_field);
            $this->assertArrayHasKey($required_field, $response['message'], 'API response message does not contain required field: ' . $required_field);
            $this->assertEquals(1, sizeof($response['message']), 'There are more than 1 items in message when not sending required field: ' . $required_field);  // https://bug.different.hu/view.php?id=6636
        }
    }

    protected function assertResponseForError($response, string $property): void
    {
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey($property, $response['message']);
        $this->assertEquals(1, sizeof($response['message']));  // https://bug.different.hu/view.php?id=6636
    }


}
