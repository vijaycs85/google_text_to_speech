Google Text To Speech

Text to Speech module uses Google Cloud Text-to-Speech library which allows you to convert words and sentences audio data of natural human speech. You can then convert the audio data into a playable audio file like an MP3. The Cloud Text-to-Speech API accepts input as raw text or Speech Synthesis Markup Language (SSML).

We recommend to install the module using composer only then the dependent libraries will be installed

composer require drupal/google_text_to_speech

Install Php Extensions bcmath & intl which are required for some features to work.

When using the official PHP images of Docker, Add these php extension by the below commands

docker-php-ext-install intl 
docker-php-ext-install bcmath 
Upload the json file to server by ftp or ssh for authentication . Make sure it is having appropriate permission and not accessible by web url.

Enable the module and add path of the json in Google text to speech configuration. You will get extra settings after hit save configuration and configure other fields too.

You can also test the text to speech feature by setting some sample text in testing part of configuration form and clicking download audio will download the audio for the text added.

After configuring the settings.

Following code can be used in other modules to generate Audio from text

//Voice Gender can be Male, Female and Neutral with respective values 1,2,3.
//Audio  Encoding  type can be LINEAR16, MP3 and OGG_OPUS  with respective values 1,2,3. 
$parameters= ["text" => "sample text","language_code"  => 'en-US',"encoding" => 2,"voice" =>1];

$gtts = \Drupal::service('google_text_to_speech.manager');
//To get Audio content
$gtts->getAudio($parameters);
//To download as audio file
$gtts->downloadAudio($parameters);
This module have dependency with media module

On Enabling this module new media type "google_text_to_speech" will be added, these media type can be used to convert the text to speech and save it as audio file. you can also duplicate the media type and add that media machine name in comma separated values in "Google Text To Speech" module configuration page.