<?php

namespace EJM\Flow\Tests\Validator;

use EJM\Flow\Network\Network;
use EJM\Flow\Network\Node\Command;
use EJM\Flow\Network\Node\Event;
use EJM\Flow\Validator\Validation;
use EJM\Flow\Validator\Validator;
use EJM\Flow\Validator\Violation;
use PHPUnit_Framework_TestCase;

class ValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $network = new Network([new Command('command', new Event('event'))]);

        $validator = new Validator();

        $validator->addConstraint($this->getNotSupportedConstraint());
        $validator->addConstraint($this->getPassedConstraint());
        $validator->addConstraint($this->getNotPassedConstraint());

        $validation = $validator->validate($network);

        $this->assertEquals(Validation::STATUS_INVALID, $validation->getStatus());
        $this->assertEquals($this->getExpectedViolations(), $validation->getViolations());
    }

    private function getNotSupportedConstraint()
    {
        $constraint = $this->getConstraintMock();

        $constraint->expects($this->once())
            ->method('supportsNode')
            ->willReturn(false);

        $constraint->expects($this->never())
            ->method('validate');

        return $constraint;
    }

    private function getPassedConstraint()
    {
        $constraint = $this->getConstraintMock();

        $constraint->expects($this->once())
            ->method('supportsNode')
            ->willReturn(true);

        $constraint->expects($this->once())
            ->method('validate')
            ->willReturn([]);

        return $constraint;
    }

    private function getNotPassedConstraint()
    {
        $constraint = $this->getConstraintMock();

        $constraint->expects($this->once())
            ->method('supportsNode')
            ->willReturn(true);

        $constraint->expects($this->once())
            ->method('validate')
            ->willReturn($this->getExpectedViolations());

        return $constraint;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getConstraintMock()
    {
        return $this->getMock('\EJM\Flow\Validator\Constraint');
    }

    /**
     * @return Violation
     */
    private function getExpectedViolations()
    {
        return [
            new Violation(new Event('event_1'), 'violation message', Violation::ERROR)
        ];
    }
}
 