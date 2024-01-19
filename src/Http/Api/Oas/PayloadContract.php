<?php

declare(strict_types=1);

namespace Projom\Http\Api\Oas;

use Projom\Util\Bools;
use Projom\Util\Json;

class PayloadContract
{
	private array $payloadContract = [];

	public function __construct(array $payloadContract = [])
	{
		$this->payloadContract = $this->parse($payloadContract);
	}
	
	public function parse(array $payloadContract): array
	{
		if (!$payloadContract)
			return [];

		$type = '';
		if ($content = $payloadContract['content'] ?? [])
			$type = key($content);

		$required = false;
		if (array_key_exists('required', $payloadContract))
			$required = Bools::toBoolean($payloadContract['required']);
		if ($required === null)
			$required = false;

		return [
			'type' => $type,
			'required' => $required
		];
	}

	public function verify(string $inputPayload): bool 
	{
		// Nothing to check.
		if (!$this->payloadContract)
			return true;

		$type = $this->payloadContract['type'];
		if (!$type)
			return false;

		if ($this->payloadContract['required']) {
			if (!$inputPayload)
				return false;
		} else {
			if (!$inputPayload)
				return true;
		}
		
		switch ($type) {
			case 'application/json':
				return Json::verify($inputPayload);
		}

		return false;
	}
}
