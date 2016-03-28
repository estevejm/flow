<?php

namespace Flow\Validator;

class Validation
{
    const STATUS_VALID = 'valid';
    const STATUS_INVALID = 'invalid';

    /**
     * @var string
     */
    private $status;

    /**
     * @var Violation[]
     */
    private $violations;

    /**
     * @param Violation[] $violations
     */
    public function __construct(array $violations)
    {
        // todo: assert all instance of violation
        $this->violations = $violations;
        $this->status = count($violations) === 0 ? self::STATUS_VALID : self::STATUS_INVALID;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return Violation[]
     */
    public function getViolations()
    {
        return $this->violations;
    }
}
 