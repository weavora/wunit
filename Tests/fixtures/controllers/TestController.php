<?php

class TestController extends CController
{

	public function actionIndex()
	{
		$this->layout = 'main';
		header('Content-type: text/html');

//		throw new Exception("dam!!");
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	public function actionContact()
	{
		$model = new ContactForm;
		if (isset($_POST['ContactForm'])) {
			$model->attributes = $_POST['ContactForm'];
			if ($model->validate()) {
				$this->refresh();
			}
		}
		$this->render('contact', array('model' => $model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model = new LoginForm;

		// if it is ajax validation request
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if (isset($_POST['LoginForm'])) {
			$model->attributes = $_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if ($model->validate() && $model->login()) {
				$this->redirect(Yii::app()->user->returnUrl);
			}
		}
		// display the login form
		$this->render('login', array('model' => $model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	public function actionRedirect()
	{
		$this->redirect(Yii::app()->createUrl('test/index'));
	}

	public function actionServer()
	{
		$this->render('server', array('server' => $_SERVER));
	}

	public function actionGlobal()
	{
		$GLOBALS['global_var'] = 123;
	}

	public function actionForm()
	{
		$model = new FullForm();

		if ($_POST['FullForm']) {
			$model->attributes = $_POST['FullForm'];
			$model->attributes = $_FILES['FullForm'];
		}

		if ($_POST['FullForm'] && $model->validate()) {
			$file = $model->fileField;
			$uploadedFile = new CUploadedFile($file['name'], $file['tmp_name'], $file['type'], $file['size'], $file['error']);
			$path = dirname(__FILE__).'/../fixtures/files/';
			$upload_result = $uploadedFile->saveAs($path."tmp.txt");
			$this->render('formSubmit', array('model' => $model, 'upload_result' => $upload_result));
		} else {
			$this->render('form', array('model' => $model));
		}
	}

	public function actionFirst()
	{
		$this->render('first');
	}

	public function actionSecond()
	{
		$this->render('second');
	}

	public function actionSetCookies()
	{
		setcookie("global_cookies", "yes");
		Yii::app()->request->cookies['yii_cookies'] = new CHttpCookie('yii_cookies', "yes");
	}

	public function actionGetCookies()
	{
		if (isset($_COOKIE['global_cookies']))
			echo "global_cookies=yes<br />";
		if (isset(Yii::app()->request->cookies['yii_cookies']))
			echo "yii_cookies=yes<br />";
	}

	public function actionSetSession()
	{
		$_SESSION['global_session'] = 'yes';
		Yii::app()->session['yii_session'] = 'yes';
	}

	public function actionGetSession()
	{
		if (isset($_SESSION['global_session']))
			echo "global_session=yes<br />";
		if (isset(Yii::app()->session['yii_session']))
			echo "yii_session=yes<br />";
	}

	public function actionYiiEnd()
	{
		echo "before<br>";
		Yii::app()->end();
		echo "after<br>";
	}

}