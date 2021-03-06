<?php
namespace IgloonetTests\MailkitApi;

use Igloonet\MailkitApi\DataObjects\Enums\MailingListStatus;
use Igloonet\MailkitApi\DataObjects\MailingList;
use Igloonet\MailkitApi\Exceptions\MailingList\MailingListExistsException;
use Igloonet\MailkitApi\Exceptions\MailingList\MailingListMissingNameException;
use Igloonet\MailkitApi\Managers\MailingListsManager;
use Igloonet\MailkitApi\RPC\Exceptions\RpcRequestFailedException;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class MailingListsManagerTest extends MailkitTestCase
{
	/** @var MailingListsManager */
	private $mailingListManager;

	protected function setUp()
	{
		parent::setUp();

		$this->mailingListManager = new MailingListsManager($this->client, ['cs'], 'cs');
	}

	public function testGetMailingLists()
	{
		$results = $this->mailingListManager->getMailingLists();

		/** @var MailingList $result */
		foreach ($results as $result) {
			Assert::same('Jméno seznamu příjemců', $result->getName());
			Assert::same(12345, $result->getId());
			Assert::same(MailingListStatus::STATUS_ENABLED, $result->getStatus()->getValue());
			Assert::same('Popis seznamu příjemců', $result->getDescription());
		}
	}

	public function testCreateMailingList()
	{
		/** @var MailingList $result */
		$result = $this->mailingListManager->createMailingList('mailingList');

		Assert::same(12345, $result->getId());
	}

	public function testCreateMissingNameMailingList()
	{
		/** @var MailingList $result */
		Assert::exception(function() {
			$this->mailingListManager->createMailingList('missingName');
		}, MailingListMissingNameException::class);
	}

	public function testCreateExistMailingList()
	{
		/** @var MailingList $result */
		Assert::exception(function() {
			$this->mailingListManager->createMailingList('exist');
		}, MailingListExistsException::class);
	}

	public function testFlushMailingList()
	{
		$result = $this->mailingListManager->flushMailingList(12345);

		Assert::true($result);
	}

	public function testDeleteMailingList()
	{
		$result = $this->mailingListManager->deleteMailingList(12345);

		Assert::true($result);
	}

	public function testDeleteMissingMailingList()
	{
		Assert::exception(function() {
			$this->mailingListManager->deleteMailingList(00000);
		}, RpcRequestFailedException::class);
	}

	public function testDeleteMailingListByName()
	{
		$result = $this->mailingListManager->deleteMailingListByName('Jméno seznamu příjemců');

		Assert::true($result);
	}

	public function testGetMailingListByName()
	{
		/** @var MailingList $result */
		$result = $this->mailingListManager->getMailingListByName('Jméno seznamu příjemců');

		Assert::same(12345, $result->getId());
	}

	public function testFindMailingListByName()
	{
		/** @var MailingList $result */
		$result = $this->mailingListManager->findMailingListByName('Jméno seznamu příjemců');

		Assert::same(12345, $result->getId());
	}
}

(new MailingListsManagerTest)->run();