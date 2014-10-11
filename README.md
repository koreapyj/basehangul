# BaseHangul

BaseHangul은 한글을 사용한 바이너리 인코더입니다.

- 한글 자모를 조합하여 나타냅니다. 사용하는 자모는 다음과 같습니다.

|자모                         |개수|종류|
|-----------------------------|----|----|
|ㄱㄴㄷㄹㅁㅂㅅㅇㅈㅊㅋㅌㅍㅎ | 14 |초성|
|ㅏㅑㅓㅕㅗㅛㅜㅠㅡㅣ         | 10 |중성|
|　ㄱㄴㄷㄹㅁㅂㅅ             |  8 |종성|

- 위 자모의 조합 중 사전순으로 앞에서부터 1024개의 조합만을 사용하여 10비트가 한 글자로 표현됩니다.
- 버그 많으니 관심좀...
- 작동 시험은 http://api.dcmys.kr/%EB%B7%81%EC%96%B4%EB%B2%88%EC%97%AD%EA%B8%B0/ 에서 해볼 수 있습니다.
- 자세한 설명은 http://api.dcmys.kr/basehangul/ 를 참고하세요.

## 예제
```php
$basehan = new BaseHangul("UTF-8");     // 인수로 출력되는 한글의 문자셋을 지정합니다. 지정하지 않으면 UTF-8로 처리됩니다.
                                        // 이 인수는 입력 내용과는 무관합니다.
$str = 'This is an encoded string';
$encoded  = $basehan->encode($str);     // 데이터를 BaseHangul로 인코딩합니다. 이 함수는 binary-safe합니다.
$plaintext= $basehan->decode($encoded); // BaseHangul을 바이너리 데이터로 복원합니다.

echo $encoded."\n";
echo $plaintext."\n";
```
실행하면 아래와 같이 출력됩니다.
```
먹잡욜카범컨겨묩눅옵툐킷박옵려녿븍캅욛웃
This is an encoded string
```
