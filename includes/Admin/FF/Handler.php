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


    public static function filter_wpcf7_form_hidden_fields($hidden_fields)
    {
        $current_form_id = \wpcf7_get_current_contact_form()->id();
        $gclidHandler = new GclidHandler();
        $gclid_enabled = $gclidHandler->get_enabled_form_lsit();
        if (in_array($current_form_id, $gclid_enabled)) {
            $hidden_fields['gclid'] = isset($_REQUEST['gclid']) ? \esc_attr($_REQUEST['gclid']) :  '';
        }
        return $hidden_fields;
    }

    public function get_a_form($data)
    {
        if (empty($data->formId)) {
            wp_send_json_error(__('Form doesn\'t exists', 'bitffzc'));
        }
        $form = wpFluent()->table('fluentform_forms')->where('id', $data->formId)->first();
        // $formProperties = new FormProperties($form);
        $fieldDetails = FormFieldsParser::getFields($form);
        if (empty($fieldDetails)) {
            wp_send_json_error(__('Form doesn\'t exists', 'bitffzc'));
        }

        $fields = [];
        foreach ($fieldDetails as  $field) {
            if (isset($field->fields)) {
                var_dump($field->fields);
            } else {
                
            }
            if (!empty($field->name) && $field->type !== 'submit') {
                $fields[] = [
                    'name' => $field->name,
                    'type' => $field->basetype,
                    'label' => !empty($field->labels) ? $field->labels[0] : $field->name,
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

    public static function handle_wpcf7_submit($form, $response)
    {
        $submission = \WPCF7_Submission::get_instance();

        if (!$submission || !$posted_data = $submission->get_posted_data()) {
            return;
        }
        if (!$submission->is('mail_failed') && !$submission->is('mail_sent')) {
            return;
        }

        if (isset($posted_data['_wpcf7'])) {
            $form_id = $posted_data['_wpcf7'];
        } else {
            $current_form = \WPCF7_ContactForm::get_current();
            $form_id = $current_form->id();
        }
        $files = $submission->uploaded_files();
        $posted_data = array_merge($posted_data, $files);
        if (!empty($form_id)) {
            Integrations::executeIntegrations($form_id, $posted_data);
        }
    }
}
