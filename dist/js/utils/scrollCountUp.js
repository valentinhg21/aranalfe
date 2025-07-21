export const scrollCountUp = (CountUp) => {
 

    let counters = document.querySelectorAll('.counter')
   
    if(counters.length > 0){
        counters.forEach(counter => {
            if (counter.id === '' && counter.dataset.value === 0) {

            } else {
            
                let options = {
                    enableScrollSpy: true
                }

                if (counter.dataset.sign != '+') {
                    if (counter.dataset.value % 1 == 0) {
                        options = {
                            enableScrollSpy: true,
                            scrollSpyOnce: true,
                            separator: '.',
                            suffix: counter.dataset.sign,
                            duration: 3,
                        }
                    } else {
                        options = {
                            enableScrollSpy: true,
                            scrollSpyOnce: true,
                            separator: '.',
                            suffix: counter.dataset.sign,
                            duration: 3,
                            decimalPlaces: 1,
                        }
                    }
                } else {
                    if (counter.dataset.value % 1 == 0) {
                        options = {
                            enableScrollSpy: true,
                            scrollSpyOnce: true,
                            separator: '.',
                            duration: 3,
                            prefix: counter.dataset.sign
                        }
                    }else{
                        options = {
                            enableScrollSpy: true,
                            scrollSpyOnce: true,
                            separator: '.',
                            duration: 3,
                            prefix: counter.dataset.sign,
                            decimalPlaces: 1,
                        }
                    }

                }

            
                if (Number(counter.dataset.value)) {
                    let countUp = new CountUp(`${counter.id}`, counter.dataset.value, options);
                }
            }
        });
    }
}