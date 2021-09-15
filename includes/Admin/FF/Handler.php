<?php

namespace BitCode\BITFFZC\Admin\FF;

use BitCode\BITFFZC\Admin\Gclid\Handler as GclidHandler;
use BitCode\BITFFZC\Integration\IntegrationHandler;
use BitCode\BITFFZC\Integration\Integrations;
use FluentForm\App\Api\FormProperties;
use FluentForm\App\Modules\Form\FormFieldsParser;

final class Handler
{
    public function __construct()
    {
        //
    }

    private function get_field_label($field)
    {
        if (property_exists($field->settings, 'label') && $field->settings->label) {
            return $field->settings->label;
        } else if (property_exists($field->settings, 'admin_field_label') && $field->settings->admin_field_label) {
            return $field->settings->admin_field_label;
        } else if (property_exists($field->attributes, 'name') && $field->attributes->name) {
            return $field->attributes->name;
        }
        return '';
    }

    public function get_a_form($data)
    {
        if (empty($data->formId)) {
            wp_send_json_error(__('Form doesn\'t exists', 'bitffzc'));
        }
        $form = wpFluent()->table('fluentform_forms')->where('id', $data->formId)->first();
        
        $fieldDetails = FormFieldsParser::getFields($form);
        if (empty($fieldDetails)) {
            wp_send_json_error(__('Form doesn\'t exists', 'bitffzc'));
        }

        $fields = [];
        foreach ($fieldDetails as  $field) {
            if (isset($field->fields)) {
                $name = isset($field->attributes->name) ? $field->attributes->name . "=>" : '';
                foreach ($field->fields as $singleField) {
                    $fields[] = [
                                    'name' => $name . $singleField->attributes->name,
                                    'type' => isset($singleField->attributes->type) ? $singleField->attributes->type : $singleField->element,
                                    'label' => $this->get_field_label($singleField),
                                ];
                }
            } else {
                $attributes = $field->attributes;
                $fields[] = [
                    'name' => $attributes->name,
                    'type' => isset($attributes->type) ? $attributes->type : $field->element,
                    'label' => $this->get_field_label($field),
                ];
            }
        }
        if (empty($fields)) {
            wp_send_json_error(__('Form doesn\'t exists any field', 'bitffzc'));
        }

        $responseData['fields'] = $fields;
        $integrationHandler = new IntegrationHandler($data->formId);
        $formIntegrations = $integrationHandler->getAllIntegration();
        if (!is_wp_error($formIntegrations)) {
            $integrations = [];
            foreach ($formIntegrations as $integrationkey => $integrationValue) {
                $integrationData = array(
                    'id' => $integrationValue->id,
                    'name' => $integrationValue->integration_name,
                    'type' => $integrationValue->integration_type,
                    'status' => $integrationValue->status,
                );
                $integrations[] = array_merge(
                    $integrationData,
                    is_string($integrationValue->integration_details) ?
                        (array) json_decode($integrationValue->integration_details) :
                        $integrationValue->integration_details
                );
            }
            $responseData['integrations'] = $integrations;
        }
        wp_send_json_success($responseData);
    }

    public static function handle_ff_submit($entryId, $formData, $form)
    {
        $form_id = $form->id;
        if (!empty($form_id)) {
            Integrations::executeIntegrations($form_id, $formData);
        }
    }
}
