# api-academic-platform

Backend de uma plataforma acadêmica (faculdade/universidade, ensino superior). Projeto de estudo do Daniel (dev backend júnior, Brasil) para praticar DDD, Value Objects, Command/Handler, testes unitários, Git Flow com PRs, CI/CD e, depois, uma API REST. O frontend (`academic-platform`, Vue 3) é outro repositório, futuro.

## Como trabalhar comigo (IMPORTANTE)

- Responda sempre em **português**, de forma simples, amigável e concisa.
- Explique **cada comando de terminal e cada flag** que sugerir.
- Passo a passo, sempre explicando o **porquê**.
- Quando eu disser que quero escrever algo sozinho (ex.: testes, um VO), **não escreva por mim**: dê um briefing e depois revise o que eu fiz.
- Faça **revisão crítica honesta** do meu código (bugs reais, não elogio).
- Não use travessões nas respostas (use vírgulas, pontos ou parênteses).
- Formato: seções com cabeçalhos, **dicas em negrito**, listas curtas, parágrafos curtos. Destaque a próxima ação.
- Nunca peça nem cole tokens, senhas ou segredos.

## Stack

PHP 8.2, Symfony 7.4 LTS, SQL Server (driver `sqlsrv` 5.12.0 via pecl), Doctrine ORM (code-first + migrations, mapping em YAML), Nginx, Docker/Docker Compose, PHPUnit 11.5, GitHub Actions.

Não uso MakerBundle (no trabalho os controllers são criados à mão).

## Ambiente (Docker)

Serviços: `app` (php-fpm), `web` (nginx, porta 8000), `database` (SQL Server, porta 1433, banco `academic_platform`).

```bash
docker compose up -d --build        # sobe tudo
docker compose exec app <comando>   # roda dentro do container app
docker compose exec app php vendor/bin/phpunit
docker compose exec app php vendor/bin/phpunit --filter "NomeDoTeste"
```

Problemas conhecidos: arquivos criados no container ficam como root (`sudo chown -R $USER:$USER .`) e `var/` pode precisar de `chmod -R 777 var`.
`.env`, `.env.dev`, `.env.test` e `.idea/` estão no `.gitignore`. Nunca versionar segredos. Na `DATABASE_URL`, o `@` da senha pode precisar de URL-encode (`%40`). `doctrine.yaml` usa `options: TrustServerCertificate: true` e `charset=UTF-8`.

## Arquitetura (DDD tático + hexagonal)

```
src/
  Domain/            # regras de negócio, sem dependência de framework/infra
    Shared/ValueObject/   BirthDate, Email, FullName
    Student/              Student, StudentId, StudentRepositoryInterface
    Enrollment/           Enrollment, EnrollmentId, EnrollmentPeriod,
                          RegistrationNumber, EnrollmentRepositoryInterface,
                          Exceptions/
  Application/       # (futuro) Commands + Handlers
  Infrastructure/    # implementações (Doctrine repositories, mapping YAML)
    Persistence/Doctrine/Mapping/   *.orm.yml
tests/               # espelha a estrutura de src/
```

Regras:
- **Domain nunca depende de Infrastructure.** Interfaces de repositório ficam no Domain, implementações na Infrastructure (inversão de dependência).
- Repositório só para **Entity**, nunca para Value Object.
- **Entity** tem identidade (typed ID). **Value Object** é imutável, igualdade por valor, auto-validado, sem identidade.
- Command + Handler (estilo CQRS, Symfony Messenger depois), não "Use Case".
- Student = a pessoa. Enrollment = vínculo temporal com a faculdade (1 aluno : N matrículas).
- Princípios: Object Calisthenics, Tell Don't Ask, SRP.

## Convenções de código

- Identificadores em **inglês**; mensagens de exceção em **português**.
- `declare(strict_types=1);` em todo arquivo (testes inclusive), classes `final`.
- Propriedades `readonly` com promoção de construtor sempre que possível.
- `\InvalidArgumentException` = valor inválido (VOs e validações de construção).
- `\DomainException` (ou exceção de domínio própria) = ação não permitida no estado atual.
- Exceção de domínio própria: mensagem fixa se for um cenário único; mensagem livre se for uma categoria.
- **Normalização na borda:** `trim` e afins acontecem no Handler/Controller ANTES de criar o VO. Os VOs **rejeitam** espaços nas pontas em vez de aceitá-los silenciosamente.
- Regra de idade mínima (16 anos) vive em `Student`; `BirthDate` é genérico (formato `Y-m-d`, não futura, máx. 120 anos).

## Testes

- Métodos em snake_case com prefixo `test_` (ex.: `test_should_throw_exception_when_name_is_empty`).
- Sempre testar: caminho feliz, `equals()` verdadeiro e falso, e **limites** (dia exato e um dia antes).
- **Sem datas fixas**: use datas relativas a hoje (ex.: helper `yearsAgo(int $years, string $adjust = '')`).
- `expectException` e `expectExceptionMessage` ANTES da linha que lança.
- `assertSame` para objetos/valores idênticos; `#[DataProvider]` para vários inputs inválidos.
- Estado atual: 63 testes verdes. Pendente: 3 testes do `FullName` (espaços nas pontas, só espaços, espaço duplo no meio aceito).

## Git Flow

- `master` = produção, `homologacao` = principal de trabalho (staging), `feature-*` = trabalho.
- Fluxo: criar feature a partir de `homologacao` → commits pequenos por assunto → push → PR para `homologacao` → depois PR `homologacao` → `master`.
- **Sempre conferir a branch base do PR** (já abri um PR para `master` por engano).
- Commits em `git add <arquivos explícitos>`, nunca `git add .`. Mensagens no estilo `feat:`, `fix:`, `refactor:`, `test:`, `chore:`.
- Proteção de branch: PR obrigatório + check `test` do GitHub Actions (`.github/workflows/tests.yml`), sem bypass, sem exigir aprovações (trabalho sozinho).

## Estado atual e próximos passos

Feito: infraestrutura Docker, CI, domínio (Student, Enrollment, VOs), conexão Doctrine com SQL Server.

Branch atual: `feature-infrastructure-doctrine-2026-10-01` (5 commits à frente de `homologacao`).

Próximos:
1. Escrever os testes pendentes do `FullName` e fazer push + PR (base `homologacao`).
2. Refazer o mapping YAML do Student com VOs (`fullName`, `email`, `birthDate` via embeddables ou custom types) e criar o do Enrollment. `src/Infrastructure/` ainda está untracked.
3. Migrations, `DoctrineStudentRepository` e `DoctrineEnrollmentRepository`; revisar `identity_generation_preferences` para SQL Server.
4. VOs `PhoneNumber` e `Cpf`; entidade `Guardian` (aluno menor de 18 precisa de responsável; decidir `GuardianId` vs objeto embutido).
5. Camada Application (Command/Handler), depois controllers REST.
6. Ideias: injetar `Clock` (fuso horário), rever a regra de "ano letivo" do `EnrollmentPeriod` para semestres, aliases de shell (`dc`, `dce`).
