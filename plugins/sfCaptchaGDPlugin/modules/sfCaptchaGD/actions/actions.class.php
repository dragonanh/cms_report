<?php

	require_once(dirname(__FILE__) . '/../../../lib/sfCaptchaGD.class.php');

	/**
	 * Captcha actions.
	 *
	 * @package    sfCaptchaGD
	 * @subpackage sfCaptchaGDActions
	 * @author     Alex Kubyshkin <glint@techinfo.net.ru>
	 * @version
	 */
	class sfCaptchaGDActions extends sfActions
	{
		public function preExecute()
		{
			#ini_set('include_path',ini_get('include_path').';'.sfConfig::get('sf_plugins_dir').'/sfMyCaptchaPlugin/ext-lib');
		}

		public function executeShowImage($request)
		{
            $img = new securimage();

			$img->image_width                  = sfConfig::get('app_sf_captchagd_image_width');
			$img->image_height                 = sfConfig::get('app_sf_captchagd_image_height');
			$img->perturbation                 = sfConfig::get('app_sf_captchagd_perturbation'); // high level of distortion
			$img->num_lines                    = sfConfig::get('app_sf_captchagd_num_lines');
			$img->noise_level                  = sfConfig::get('app_sf_captchagd_noise_level');
			$img->charset                      = sfConfig::get('app_sf_captchagd_charset');
			$img->text_transparency_percentage = sfConfig::get('app_sf_captchagd_text_transparency_percentage');
			$img->use_transparent_text         = sfConfig::get('app_sf_captchagd_use_transparent_text');
			$code_length_min                   = sfConfig::get('app_sf_captchagd_code_length_min');
			$code_length_max                   = sfConfig::get('app_sf_captchagd_code_length_max');
			$img->code_length                  = rand($code_length_min, $code_length_max); // random code length
			$img->image_bg_color               = new Securimage_Color(sfConfig::get('app_sf_captchagd_image_bg_color'));
			$img->text_color                   = new Securimage_Color(sfConfig::get('app_sf_captchagd_text_color'));
			$img->line_color                   = new Securimage_Color(sfConfig::get('app_sf_captchagd_line_color'));
			$img->noise_color                  = $img->text_color;
            $img->namespace                    = 'default';
			$img->show();
		}

    public function executeShowImageEasy($request)
    {
      $img = new securimage();

      $img->image_width                  = sfConfig::get('app_sf_captchagd_easy_image_width');
      $img->image_height                 = sfConfig::get('app_sf_captchagd_easy_image_height');
      $img->perturbation                 = sfConfig::get('app_sf_captchagd_easy_perturbation'); // high level of distortion
			$img->num_lines                    = sfConfig::get('app_sf_captchagd_easy_num_lines');
			$img->noise_level                  = sfConfig::get('app_sf_captchagd_easy_noise_level');
      $img->charset                      = sfConfig::get('app_sf_captchagd_easy_charset');
      $img->text_transparency_percentage = sfConfig::get('app_sf_captchagd_easy_text_transparency_percentage');
      $img->use_transparent_text         = sfConfig::get('app_sf_captchagd_easy_use_transparent_text');
      $code_length_min                   = sfConfig::get('app_sf_captchagd_easy_code_length_min');
      $code_length_max                   = sfConfig::get('app_sf_captchagd_easy_code_length_max');
      $img->code_length                  = rand($code_length_min, $code_length_max); // random code length
      $img->image_bg_color               = new Securimage_Color(sfConfig::get('app_sf_captchagd_easy_image_bg_color'));
      $img->text_color                   = new Securimage_Color(sfConfig::get('app_sf_captchagd_easy_text_color'));
      $img->line_color                   = new Securimage_Color(sfConfig::get('app_sf_captchagd_easy_line_color'));
      $img->noise_color                  = $img->text_color;
      $img->namespace                    = 'default';
      $img->show();
    }

		public function executePlay_sound(sfWebRequest $request)
		{
			$img               = new Securimage();
			$img->audio_format = ($request->getParameter('format') && in_array(strtolower($request->getParameter('format')), array('mp3', 'wav')) ? strtolower($request->getParameter('format')) : 'mp3');
			$img->outputAudioFile();
		}
	}
