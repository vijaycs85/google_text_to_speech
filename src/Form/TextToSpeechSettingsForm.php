<?php
/**
 * @author Karthikeyan Manivasagam
 * @author Karthikeyan Manivasagam <karthikeyanm.inbox@gmail.com>
 */
namespace Drupal\google_text_to_speech\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure text to speech settings.
 */
class TextToSpeechSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'google_text_to_speech_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'google_text_to_speech.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('google_text_to_speech.settings');

    $form['google_text_to_speech_json_path'] = [
      '#title' => $this->t('Credentials Json path'),
      '#type' => 'textfield',
      '#description' => $this->t("Enter the path of the json which is shared by Google Cloud"),
      '#default_value' => $config->get('google_text_to_speech_json_path'),
      '#required' => true,
    ];

    $form['google_text_to_speech_language_list'] = ["#type" => 'textarea', "#title" => t('Allowed language list'), "#description" => 'The possible languages this field can contain. Enter one value per line, in the format key|label that is languagecode|language (eg). en-GB|English (UK). You can get the list of supported languages and voices from  <a target="_blank" href="https://cloud.google.com/text-to-speech/docs/voices"><b>Google Support Docs link</b></a>', "#default_value" => $config->get('google_text_to_speech_language_list'), '#required' => true,];

    if($config->get('google_text_to_speech_json_path') != "" && $config->get('google_text_to_speech_language_list') != "") {
      $list = $config->get('google_text_to_speech_language_list'); 
      $languageConfig = explode("\n", $list);
      $languageConfig = array_filter($languageConfig);
      $languageOption = array();
      array_walk($languageConfig, function($val,$key) use(&$languageOption){
          list($key, $value) = explode('|', $val);
          $languageOption[$key] = $value;
      });
      $form['google_text_to_speech_language'] = [
        '#title' => $this->t('Language'),
        '#type' => 'select',
        '#description' => $this->t("It will generate the audio in selected language for the above text"),
        '#default_value' => $config->get('google_text_to_speech_language'),
         '#options' =>   $languageOption,
         '#required' => true,
      ];

      $form['google_text_to_speech_voice'] = [
        '#title' => $this->t('Voice Gender'),
        '#type' => 'select',
        '#description' => $this->t("It will generate the audio in the selected voice for the above text"),
        '#default_value' => $config->get('google_text_to_speech_voice'),
        '#options' =>  [1 => "Male", 2 => "Female",
      3 => "Neutral"],
        '#required' => true,
      ];

      $form['google_text_to_speech_encoding'] = [
        '#title' => $this->t('Audio Encoding'),
        '#type' => 'select',
        '#description' => $this->t("It will generate the audio in the selected encoding format"),
        '#default_value' => $config->get('google_text_to_speech_encoding'),
        '#options' =>  [1 => 'Linear 16', 3 => 'WAVNET', 2 => 'Mp3'],
        '#required' => true,
      ];
      $form['google_text_to_speech_paragraphs'] = ["#type" => 'textarea', "#title" => t('Allowed paragraph list'), "#description" => 'Enter the list of paragraphs that has to be considered as google text to speech paragraph, enter  the paragraphs comma seperated values (eg) google_text_to_speech, additonal_paragraph, one_more_custom', "#default_value" => $config->get('google_text_to_speech_paragraphs')];
      $form['test'] = array(
            '#type' => 'details',
            '#title' => $this->t('Test the Above Config'),
            '#open' => FALSE,
      );

      $form['test']['google_text_to_speech_text'] = [
        '#title' => $this->t('Add Sample Text'),
        '#type' => 'textarea',
        '#description' => $this->t("Enter some word or sentence to generate Sample Audio for the above config"),
        '#default_value' => $config->get('google_text_to_speech_text'),
      ];

      $form['test']['google_text_to_speech_test_now'] = [
        '#type' => 'submit',
        '#value' => $this->t('Download Audio'), 
        '#description' => $this->t("It will generate the audio for the above configuration"),
        '#submit' => array('::generateAudio'),
      ];
   }
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('google_text_to_speech.settings')
      ->set('google_text_to_speech_json_path', $form_state->getValue('google_text_to_speech_json_path'))
     ->set('google_text_to_speech_text', $form_state->getValue('google_text_to_speech_text'))
      ->set('google_text_to_speech_language_list', $form_state->getValue('google_text_to_speech_language_list'))
      ->set('google_text_to_speech_language', $form_state->getValue('google_text_to_speech_language'))
      ->set('google_text_to_speech_voice', $form_state->getValue('google_text_to_speech_voice'))
      ->set('google_text_to_speech_encoding', $form_state->getValue('google_text_to_speech_encoding'))
      ->set('google_text_to_speech_paragraphs', $form_state->getValue('google_text_to_speech_paragraphs'))
      ->save(); 
    parent::submitForm($form, $form_state);
  }

  public function generateAudio(array &$form, FormStateInterface $form_state) {
     if($form_state->getValue('google_text_to_speech_test_now') == true){
        $parameters= ["text" => $form_state->getValue('google_text_to_speech_text'),"language_code"  => $form_state->getValue('google_text_to_speech_language'),"encoding" => $form_state->getValue('google_text_to_speech_encoding'),"voice" => $form_state->getValue('google_text_to_speech_voice')];
        $gtts = \Drupal::service('google_text_to_speech.manager');
        $gtts->downloadAudio($parameters);
      }
  }  

}
