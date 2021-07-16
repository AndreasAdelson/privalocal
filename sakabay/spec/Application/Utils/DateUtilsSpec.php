<?php

namespace spec\App\Application\Utils;

use App\Application\Utils\DateUtils;
use PhpSpec\ObjectBehavior;

class DateUtilsSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(DateUtils::class);
    }

    public function it_should_formatedDate()
    {
        $date = \DateTime::createFromFormat('Y/m/d H:i:s', '2018/07/01 00:00:00');
        $this::formatedDate($date)->shouldReturn('2018-07-01 00:00:00');
        $this::formatedDate(null, '')->shouldReturn('');
        $this::formatedDate($date, '', 'Y m d')->shouldReturn('2018 07 01');
    }

    public function it_should_frenchmonths()
    {
        $this::frenchMonths()->shouldBeArray();
    }

    public function it_should_get_forms_dates_options()
    {
        $this::getFormsDatesOptions()->shouldBeArray();
    }

    public function it_should_diff()
    {
        $this::diff(null, null)->shouldReturn(false);
        $date1 = \DateTime::createFromFormat('Y-m-d H:i:s', '2018-07-01 14:10:07');
        $date2 = \DateTime::createFromFormat('Y-m-d H:i:s', '2018-06-15 18:30:15');
        $this::diff($date1, null)->shouldReturn(true);
        $this::diff(null, $date1)->shouldReturn(true);

        $this::diff($date1, $date1)->shouldReturn(false);
        $this::diff($date1, $date2)->shouldReturn(true);
    }
}
