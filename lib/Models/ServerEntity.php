<?php

namespace OCA\Nextmail\Models;

use JsonSerializable;
use OCA\Nextmail\ResponseDefinitions;
use OCA\Nextmail\SchemaV1\SchServer;
use ValueError;

/** @psalm-import-type NextmailServer from ResponseDefinitions */
readonly class ServerEntity implements JsonSerializable {
	private const URL_PATTERN = '/^https?:\\/\\/([a-z0-9-]+\\.)*[a-z0-9-]+(:\\d{1,5})?\\/api$/';


	public function __construct(
		public string       $id,
		public string       $endpoint,
		public string       $username,
		public string       $password,
		public ServerHealth $health,
	) {
	}

	public static function newEmpty(): self {
		return new self(str_replace('.', '_', uniqid('nc_', true)), '', '', '', ServerHealth::Invalid);
	}

	public static function parse(mixed $value): ServerEntity {
		if (!is_array($value)) {
			throw new ValueError('value must be an array');
		}
		if (!is_string($value[SchServer::ID])) {
			throw new ValueError('id must be a string');
		}
		if (!is_string($value[SchServer::ENDPOINT])) {
			throw new ValueError('endpoint must be a string');
		}
		if (!is_string($value[SchServer::USERNAME])) {
			throw new ValueError('username must be a string');
		}
		if (!is_string($value[SchServer::PASSWORD])) {
			throw new ValueError('password must be a string');
		}
		if (!is_string($value[SchServer::HEALTH])) {
			throw new ValueError('health must be a string');
		}
		return new self(
			$value[SchServer::ID],
			$value[SchServer::ENDPOINT],
			$value[SchServer::USERNAME],
			$value[SchServer::PASSWORD],
			ServerHealth::from($value[SchServer::HEALTH]),
		);
	}

	/** @return NextmailServer */
	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'endpoint' => $this->endpoint,
			'username' => $this->username,
			'health' => $this->health->value,
		];
	}

	public function getUrl(string $subpart = ''): ?string {
		return preg_match(self::URL_PATTERN, $this->endpoint) === 1
			? $this->endpoint . $subpart
			: null;
	}

	public function getBasicAuth(): ?string {
		return $this->username !== '' && $this->password !== ''
			? 'Basic ' . base64_encode($this->username . ':' . $this->password)
			: null;
	}

	public function updateCredential(string $endpoint, string $username, string $password): self {
		return new self(
			$this->id,
			$endpoint,
			$username,
			$password !== '' ? $password : $this->password,
			$this->health,
		);
	}

	public function updateHealth(ServerHealth $health): self {
		return new self(
			$this->id,
			$this->endpoint,
			$this->username,
			$this->password,
			$health,
		);
	}
}
