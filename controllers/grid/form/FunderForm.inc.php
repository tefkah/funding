<?php

/**
 * @file plugins/generic/fundRef/controllers/grid/form/FunderForm.inc.php
 *
 * Copyright (c) 2014-2017 Simon Fraser University
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class FunderForm
 * @ingroup controllers_grid_fundRef
 *
 * Form for adding/editing a funder
 *
 */

import('lib.pkp.classes.form.Form');

class FunderForm extends Form {
	/** @var int Context ID */
	var $contextId;

	/** @var int Submission ID */
	var $submissionId;

	/** @var FundRefPlugin */
	var $plugin;

	/**
	 * Constructor
	 * @param $fundRefPlugin FundRefPlugin
	 * @param $contextId int Context ID
	 * @param $submissionId int Submission ID
	 * @param $funderId int (optional) Funder ID
	 */
	function __construct($fundRefPlugin, $contextId, $submissionId, $funderId = null) {
		parent::__construct($fundRefPlugin->getTemplatePath() . 'editFunderForm.tpl');

		$this->contextId = $contextId;
		$this->submissionId = $submissionId;
		$this->funderId = $funderId;
		$this->plugin = $fundRefPlugin;

		// Add form checks
		$this->addCheck(new FormValidator($this, 'funderNameIdentification', 'required', 'plugins.generic.fundRef.funderNameIdentificationRequired'));
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));

	}

	/**
	 * @copydoc Form::initData()
	 */
	function initData() {
		$this->setData('submissionId', $this->submissionId);
		if ($this->funderId) {
			$funderDao = DAORegistry::getDAO('FunderDAO');
			$funder = $funderDao->getById($this->funderId);
			$this->setData('funderNameIdentification', $funder->getFunderNameIdentification());
			$this->setData('funderGrants', $funder->getFunderGrants());
		}
	}

	/**
	 * @copydoc Form::readInputData()
	 */
	function readInputData() {
		$this->readUserVars(array('funderNameIdentification', 'funderGrants'));
	}

	/**
	 * @copydoc Form::fetch
	 */
	function fetch($request) {
		$templateMgr = TemplateManager::getManager();
		$templateMgr->assign('funderId', $this->funderId);
		return parent::fetch($request);
	}

	/**
	 * Save form values into the database
	 */
	function execute() {
		$funderDao = DAORegistry::getDAO('FunderDAO');

		if ($this->funderId) {
			// Load and update an existing funder
			$funder = $funderDao->getById($this->funderId, $this->submissionId);
		} else {
			// Create a new
			$funder = $funderDao->newDataObject();
			$funder->setContextId($this->contextId);
			$funder->setSubmissionId($this->submissionId);
		}

		$funderName = "";
		$funderIdentification = "";
		$funderNameIdentification = $this->getData('funderNameIdentification');

		if ($funderNameIdentification != ""){
			$funderName = trim(preg_replace('/\s*\[.*?\]\s*/ ', '', $funderNameIdentification));
			if (preg_match("/\[(.*?)\]/", $funderNameIdentification, $output))
				$funderIdentification = $output[1];
		}

		$funder->setFunderName($funderName);
		$funder->setFunderIdentification($funderIdentification);
		$funder->setFunderGrants($this->getData('funderGrants'));

		if ($this->funderId) {
			$funderDao->updateObject($funder);
		} else {
			$funderDao->insertObject($funder);
		}
	}
}

?>
